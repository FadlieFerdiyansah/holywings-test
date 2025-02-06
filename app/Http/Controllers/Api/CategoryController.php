<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        return api_response('Categories retrieved successfully.', Category::get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|unique:categories']);
        $category = Category::create($validated);

        return api_response('Category created successfully.', $category);
    }

    public function update(Category $category, Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|unique:categories',
        ]);

        $category->update($validated);

        return api_response('Category updated successfully', $category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return api_response('Category deleted successfully');
    }
}
