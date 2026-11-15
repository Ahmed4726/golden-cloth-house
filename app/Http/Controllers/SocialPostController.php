<?php

namespace App\Http\Controllers;

use App\Jobs\PublishSocialPost;
use App\Models\Product;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialPostController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:draft,scheduled,queued,processing,published,failed'],
        ]);
        $search = trim($filters['q'] ?? '');

        return view('posts.index', [
            'posts' => SocialPost::withCount('publications')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('caption', 'like', "%{$search}%")
                            ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('posts.create', [
            'connections' => SocialConnection::where('active', true)->get(),
            'products' => Product::with(['categoryRelation', 'brand'])
                ->where('active', true)
                ->latest()
                ->get(),
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

        $product = ! empty($data['product_id']) ? Product::findOrFail($data['product_id']) : null;
        $caption = trim($data['caption'] ?? '');
        $title = trim($data['title'] ?? '');
        $rawUrls = trim($data['media_urls'] ?? '');

        if ($product) {
            $title = $title ?: $product->name;
            $caption = $caption ?: $product->description ?: $product->name;
            $rawUrls = $rawUrls ?: implode("\n", $this->productMediaUrls($product));
        }

        if ($data['media_type'] === 'image' && ! $rawUrls) {
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
            ['media_urls' => ['array', 'max:10'], 'media_urls.*' => ['url', 'max:2048']]
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

        $post = DB::transaction(function () use ($data, $urls, $status, $caption, $title) {
            $post = SocialPost::create([
                'product_id' => $data['product_id'] ?? null,
                'title' => $title ?: null,
                'caption' => $caption,
                'media_type' => $data['media_type'],
                'media_urls' => $urls,
                'video_url' => $data['video_url'] ?? null,
                'status' => $status,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'created_by' => session('admin_user_id'),
            ]);

            foreach ($data['connections'] as $id) {
                $connection = SocialConnection::whereKey($id)->where('active', true)->firstOrFail();
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
            $publication->update(['status' => 'queued', 'error' => null]);
            PublishSocialPost::dispatch($publication->id);
        }

        $post->update(['status' => 'queued']);

        return back()->with('success', 'Publishing queued.');
    }

    private function productMediaUrls(Product $product): array
    {
        $urls = $product->image_urls ?? [];

        foreach ($product->media_paths ?? [] as $path) {
            if ($product->imageExists($path)) {
                $urls[] = url($product->imageUrl($path));
            }
        }

        return array_values(array_unique($urls));
    }
}
