<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $sourceModifiedAt = $disk->lastModified($path);
        $sourceSize = $disk->size($path);
        $variantPath = 'responsive/'.sha1($path.'|'.$sourceModifiedAt.'|'.$sourceSize.'|'.$width).'.webp';

        if (! $disk->exists($variantPath)) {
            $this->createWebpVariant($path, $variantPath, (int) $width);
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

    private function createWebpVariant(string $sourcePath, string $variantPath, int $targetWidth): void
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            Log::warning('Responsive image conversion is unavailable because GD WebP support is missing.', [
                'path' => $sourcePath,
            ]);

            return;
        }

        $sourceBytes = Storage::disk('public')->get($sourcePath);

        if ($sourceBytes === false) {
            Log::warning('Responsive image conversion could not read the source image.', [
                'path' => $sourcePath,
            ]);

            return;
        }

        $sourceImage = @imagecreatefromstring($sourceBytes);

        if ($sourceImage === false) {
            Log::warning('Responsive image conversion failed to decode the source image.', [
                'path' => $sourcePath,
            ]);

            return;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $width = $targetWidth;
        $height = max(1, (int) round($sourceHeight * ($width / $sourceWidth)));
        $variantImage = imagecreatetruecolor($width, $height);
        imagealphablending($variantImage, false);
        imagesavealpha($variantImage, true);
        imagefill(
            $variantImage,
            0,
            0,
            imagecolorallocatealpha($variantImage, 255, 255, 255, 127)
        );
        imagecopyresampled(
            $variantImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $sourceWidth,
            $sourceHeight
        );

        ob_start();
        $converted = imagewebp($variantImage, null, 78);
        $webpBytes = ob_get_clean();
        imagedestroy($sourceImage);
        imagedestroy($variantImage);

        if (! $converted || $webpBytes === false || $webpBytes === '') {
            Log::warning('Responsive image conversion failed to encode WebP.', [
                'path' => $sourcePath,
            ]);

            return;
        }

        if (! Storage::disk('public')->put($variantPath, $webpBytes)) {
            Log::warning('Responsive WebP image could not be cached.', [
                'path' => $sourcePath,
            ]);
        }
    }
}
