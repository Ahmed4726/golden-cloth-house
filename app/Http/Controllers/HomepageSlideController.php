<?php

namespace App\Http\Controllers;

use App\Models\HomepageSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageSlideController extends Controller
{
    public function index()
    {
        return view('slides.index', [
            'slides' => HomepageSlide::orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('slides.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateSlide($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('slides', 'public');
        }

        $data['active'] = $request->boolean('active', true);
        HomepageSlide::create($data);

        return redirect()->route('slides.index')->with('success', 'Homepage slide created.');
    }

    public function edit(HomepageSlide $slide)
    {
        return view('slides.edit', compact('slide'));
    }

    public function update(Request $request, HomepageSlide $slide)
    {
        $data = $this->validateSlide($request);

        if ($request->hasFile('image')) {
            if ($slide->image_path) {
                Storage::disk('public')->delete($slide->image_path);
            }

            $data['image_path'] = $request->file('image')->store('slides', 'public');
        }

        $data['active'] = $request->boolean('active');
        $slide->update($data);

        return redirect()->route('slides.index')->with('success', 'Homepage slide updated.');
    }

    public function destroy(HomepageSlide $slide)
    {
        if ($slide->image_path) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->delete();

        return back()->with('success', 'Homepage slide removed.');
    }

    private function validateSlide(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);
    }
}
