<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,hidden'],
        ]);
        $search = trim($filters['q'] ?? '');

        return view('brands.index', [
            'brands' => Brand::withCount('products')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
                })
                ->when(isset($filters['status']), fn ($query) => $query->where('active', $filters['status'] === 'active'))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateBrand($request);
        $logo = $request->file('logo')?->store('brands', 'public');

        Brand::create([
            ...$data,
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'logo_path' => $logo,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $this->validateBrand($request, $brand);
        $logo = $brand->logo_path;

        if ($request->hasFile('logo')) {
            if ($logo) {
                Storage::disk('public')->delete($logo);
            }
            $logo = $request->file('logo')->store('brands', 'public');
        }

        $brand->update([
            ...$data,
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'logo_path' => $logo,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->logo_path) {
            Storage::disk('public')->delete($brand->logo_path);
        }

        $brand->delete();

        return back()->with('success', 'Brand deleted successfully.');
    }

    private function validateBrand(Request $request, ?Brand $brand = null): array
    {
        $id = $brand?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:brands,name,'.$id],
            'slug' => ['nullable', 'string', 'max:140', 'unique:brands,slug,'.$id],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}
