<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function categories()
    {
        return response()->json([
            'categories' =>
                Category::orderBy('name')->get(),
        ]);
    }

    public function products(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0);

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'brand',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'compatible_models',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $products = $query
            ->orderBy('name')
            ->paginate(20);

        return response()->json(
            $products
        );
    }

    public function show(Product $product)
    {
        abort_unless(
            $product->is_active,
            404
        );

        $product->load('category');

        return response()->json([
            'product' => $product,
        ]);
    }
}