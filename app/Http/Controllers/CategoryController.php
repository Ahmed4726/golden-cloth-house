<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', [
            'categories' => Category::withCount('products')->orderBy('sort_order')->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:140', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Category::create([
            ...$data,
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name,'.$category->id],
            'slug' => ['nullable', 'string', 'max:140', 'unique:categories,slug,'.$category->id],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category->update([
            ...$data,
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
