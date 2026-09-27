<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->paginate(10);

        return view(
            'admin.categories.index',
            compact('categories')
        );
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $category = Category::create($validated);

        AuditLogger::log(
            action: 'created_category',
            module: 'Categories',
            record: $category,
            description:
                "Created category {$category->name}."
        );

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category created successfully.'
            );
    }

    public function edit(Category $category)
    {
        return view(
            'admin.categories.edit',
            compact('category')
        );
    }

    public function update(
        Request $request,
        Category $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $category->id,
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $category->update($validated);

        AuditLogger::log(
            action: 'updated_category',
            module: 'Categories',
            record: $category,
            description:
                "Updated category {$category->name}."
        );

        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Category updated successfully.'
            );
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with(
                'error',
                'Cannot delete a category that contains products.'
            );
        }

        $category->delete();

        return back()->with(
            'success',
            'Category deleted successfully.'
        );
    }
}