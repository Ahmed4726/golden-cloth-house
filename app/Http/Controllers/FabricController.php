<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FabricController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($filters['q'] ?? '');

        $fabrics = Fabric::query()
            ->withCount(['kameezProducts', 'trouserProducts', 'dupattaProducts'])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('fabrics.index', compact('fabrics'));
    }

    public function create(): View
    {
        return view('fabrics.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        Fabric::create([
            ...$data,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('fabrics.index')->with('success', 'Fabric created.');
    }

    public function edit(Fabric $fabric): View
    {
        return view('fabrics.edit', compact('fabric'));
    }

    public function update(Request $request, Fabric $fabric): RedirectResponse
    {
        $data = $this->validatedData($request, $fabric);
        $fabric->update([
            ...$data,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('fabrics.index')->with('success', 'Fabric updated.');
    }

    public function destroy(Fabric $fabric): RedirectResponse
    {
        $fabric->delete();

        return back()->with('success', 'Fabric deleted.');
    }

    /**
     * @return array{name: string, sort_order: int}
     */
    private function validatedData(Request $request, ?Fabric $fabric = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('fabrics', 'name')->ignore($fabric?->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);
    }
}
