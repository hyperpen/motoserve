<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'sku',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'brand',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $products = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view(
            'admin.products.index',
            compact('products')
        );
    }

    public function create()
    {
        $categories = Category::orderBy('name')
            ->get();

        return view(
            'admin.products.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:products,sku',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'brand' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'compatible_models' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'image' => [
                'nullable',
                'image',
                'max:4096',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $validated
        ) {
            if ($request->hasFile('image')) {
                $validated['image'] =
                    $request->file('image')
                        ->store(
                            'products',
                            'public'
                        );
            }

            $product = Product::create($validated);

            if ($product->stock > 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'stock_in',
                    'quantity' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'remarks' => 'Initial stock.',
                ]);
            }

            AuditLogger::log(
                action: 'created_product',
                module: 'Products',
                record: $product,
                description:
                    "Created product {$product->name}."
            );
        });

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product created successfully.'
            );
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')
            ->get();

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    public function update(
        Request $request,
        Product $product
    ) {
        $validated = $request->validate([
            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique(
                    'products',
                    'sku'
                )->ignore($product),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'brand' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'compatible_models' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'image' => [
                'nullable',
                'image',
                'max:4096',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')
                    ->delete($product->image);
            }

            $validated['image'] =
                $request->file('image')
                    ->store(
                        'products',
                        'public'
                    );
        }

        $product->update($validated);

        AuditLogger::log(
            action: 'updated_product',
            module: 'Products',
            record: $product,
            description:
                "Updated product {$product->name}."
        );

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    public function stock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'operation' => [
                'required',
                Rule::in([
                    'add',
                    'subtract',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $product
        ) {
            $product = Product::lockForUpdate()
                ->findOrFail($product->id);

            $before = $product->stock;

            if (
                $validated['operation'] ===
                'subtract'
            ) {
                if (
                    $validated['quantity'] >
                    $product->stock
                ) {
                    abort(
                        422,
                        'Not enough stock.'
                    );
                }

                $product->stock -=
                    $validated['quantity'];
            } else {
                $product->stock +=
                    $validated['quantity'];
            }

            $product->save();

            InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),

                'type' =>
                    $validated['operation'] === 'add'
                        ? 'stock_in'
                        : 'adjustment',

                'quantity' =>
                    $validated['operation'] === 'add'
                        ? $validated['quantity']
                        : -$validated['quantity'],

                'stock_before' => $before,
                'stock_after' => $product->stock,

                'remarks' =>
                    $validated['remarks']
                    ?? 'Manual stock adjustment.',
            ]);
        });

        return back()->with(
            'success',
            'Stock updated successfully.'
        );
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return back()->with(
            'success',
            'Product status updated.'
        );
    }
}