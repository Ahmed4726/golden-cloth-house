<?php

namespace App\Http\Controllers;

use App\Jobs\PublishSocialPost;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialPostController extends Controller
{
    public function index()
    {
        return view('posts.index', ['posts' => SocialPost::with('publications.connection')->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('posts.create', ['connections' => SocialConnection::where('active', true)->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable','string','max:255'],
            'caption' => ['required','string','max:5000'],
            'media_type' => ['required','in:image,video'],
            'media_urls' => ['nullable','array','max:10'],
            'media_urls.*' => ['url'],
            'video_url' => ['nullable','url'],
            'connections' => ['required','array','min:1'],
            'connections.*' => ['integer','exists:social_connections,id'],
            'action' => ['required','in:draft,publish,schedule'],
            'scheduled_at' => ['nullable','date','after:now'],
        ]);

        $status = $data['action'] === 'draft' ? 'draft' : ($data['action'] === 'schedule' ? 'scheduled' : 'queued');

        $post = DB::transaction(function () use ($data, $status) {
            $post = SocialPost::create([
                'title' => $data['title'] ?? null,
                'caption' => $data['caption'],
                'media_type' => $data['media_type'],
                'media_urls' => $data['media_urls'] ?? [],
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

        return redirect()->route('posts.show', $post)->with('success', $status === 'queued' ? 'Publishing has been queued.' : 'Post saved.');
    }

    public function show(SocialPost $post)
    {
        return view('posts.show', ['post' => $post->load('publications.connection')]);
    }

    public function publish(SocialPost $post)
    {
        foreach ($post->publications()->whereIn('status', ['queued','failed','draft'])->get() as $publication) {
            $publication->update(['status' => 'queued', 'error' => null]);
            PublishSocialPost::dispatch($publication->id);
        }

        $post->update(['status' => 'queued']);

        return back()->with('success', 'Publishing queued.');
    }
}
