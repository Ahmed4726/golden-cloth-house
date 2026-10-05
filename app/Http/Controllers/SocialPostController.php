<?php

namespace App\Http\Controllers;

use App\Jobs\PublishSocialPost;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SocialPostController extends Controller
{
    public function index()
    {
        return view('posts.index', [
            'posts' => SocialPost::with(['publications.connection', 'product'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create()
    {
        return view('posts.create', [
            'connections' => SocialConnection::where('active', true)->get(),
            'products' => Product::where('active', true)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:5000'],
            'media_type' => ['required', 'in:image,video'],
            'media_urls' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'connections' => ['required', 'array', 'min:1'],
            'connections.*' => ['integer', 'exists:social_connections,id'],
            'action' => ['required', 'in:draft,publish,schedule'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);

        $product = ! empty($data['product_id'])
            ? Product::findOrFail($data['product_id'])
            : null;

        $caption = $data['caption'] ?? '';
        $rawUrls = $data['media_urls'] ?? '';

        if ($product) {
            $caption = $caption ?: $product->name.' — '.$product->description;
            $data['title'] = $data['title'] ?? $product->name;

            if (! trim($rawUrls)) {
                $rawUrls = implode("\n", $this->productMediaUrls($product));
            }
        }

        if ($data['media_type'] === 'image' && ! trim($rawUrls)) {
            return back()
                ->withErrors(['media_urls' => 'At least one product image or image URL is required.'])
                ->withInput();
        }

        $urls = collect(preg_split('/\r?\n/', $rawUrls))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->values()
            ->all();

        validator(
            ['media_urls' => $urls],
            [
                'media_urls' => ['array', 'max:10'],
                'media_urls.*' => ['url'],
            ]
        )->validate();

        if ($data['action'] === 'schedule' && empty($data['scheduled_at'])) {
            return back()
                ->withErrors(['scheduled_at' => 'A future date/time is required.'])
                ->withInput();
        }

        $status = match ($data['action']) {
            'draft' => 'draft',
            'schedule' => 'scheduled',
            default => 'queued',
        };

        $post = DB::transaction(function () use ($data, $urls, $status, $caption) {
            $post = SocialPost::create([
                'product_id' => $data['product_id'] ?? null,
                'title' => $data['title'] ?? null,
                'caption' => $caption,
                'media_type' => $data['media_type'],
                'media_urls' => $urls,
                'video_url' => $data['video_url'] ?? null,
                'status' => $status,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'created_by' => session('admin_user_id'),
            ]);

            foreach ($data['connections'] as $id) {
                $connection = SocialConnection::whereKey($id)
                    ->where('active', true)
                    ->firstOrFail();

                $publication = $post->publications()->create([
                    'social_connection_id' => $connection->id,
                    'provider' => $connection->provider,
                    'status' => $status === 'draft' ? 'draft' : 'queued',
                ]);

                if ($status === 'queued') {
                    PublishSocialPost::dispatch($publication->id);
                }
            }

            return $post;
        });

        return redirect()
            ->route('posts.show', $post)
            ->with('success', $status === 'queued' ? 'Publishing has been queued.' : 'Post saved.');
    }

    public function show(SocialPost $post)
    {
        return view('posts.show', [
            'post' => $post->load(['publications.connection', 'product']),
        ]);
    }

    public function publish(SocialPost $post)
    {
        foreach ($post->publications()->whereIn('status', ['queued', 'failed', 'draft'])->get() as $publication) {
            $publication->update([
                'status' => 'queued',
                'error' => null,
            ]);

            PublishSocialPost::dispatch($publication->id);
        }

        $post->update(['status' => 'queued']);

        return back()->with('success', 'Publishing queued.');
    }

    private function productMediaUrls(Product $product): array
    {
        $urls = $product->image_urls ?? [];

        foreach ($product->media_paths ?? [] as $path) {
            $urls[] = url(Storage::disk('public')->url($path));
        }

        return array_values(array_unique($urls));
    }
}
