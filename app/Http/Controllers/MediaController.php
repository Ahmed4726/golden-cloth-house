<?php

namespace App\Http\Controllers;

use App\Services\ResponsiveImageCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function show(Request $request, string $path): Response
    {
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        $width = $request->query('width');

        if ($width === null) {
            return $disk->response($path, basename($path), [
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        abort_unless(in_array((string) $width, ['320', '480', '640', '960', '1280'], true), 404);

        $variantPath = ResponsiveImageCache::variantPath($path, (int) $width);

        if (! $disk->exists($variantPath)) {
            ResponsiveImageCache::createVariant($path, (int) $width);
        }

        if (! $disk->exists($variantPath)) {
            return $disk->response($path, basename($path), [
                'Cache-Control' => 'public, max-age=604800',
            ]);
        }

        return $disk->response($variantPath, basename($variantPath), [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Content-Type' => 'image/webp',
        ]);
    }
}
