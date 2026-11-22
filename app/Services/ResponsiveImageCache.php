<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResponsiveImageCache
{
    public const WIDTHS = [320, 480, 640, 960, 1280];

    public static function sourcePath(string $image): ?string
    {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return null;
        }

        return ltrim(preg_replace('#^/?storage/#', '', $image), '/');
    }

    public static function variantPath(string $sourcePath, int $width): string
    {
        // Uploaded paths are unique, so rendering a srcset needs no source metadata lookups.
        return 'responsive/'.sha1($sourcePath.'|'.$width).'.webp';
    }

    public static function urlFor(string $image, int $width): string
    {
        $sourcePath = self::sourcePath($image);

        if ($sourcePath === null) {
            return $image;
        }

        $disk = Storage::disk('public');
        $variantPath = self::variantPath($sourcePath, $width);

        if ($disk->exists($variantPath)) {
            return $disk->url($variantPath);
        }

        return route('media.show', [
            'path' => $sourcePath,
            'width' => $width,
        ]);
    }

    public static function createVariant(string $sourcePath, int $targetWidth): bool
    {
        $disk = Storage::disk('public');
        $variantPath = self::variantPath($sourcePath, $targetWidth);

        if ($disk->exists($variantPath)) {
            return true;
        }

        if (! $disk->exists($sourcePath)) {
            Log::warning('Responsive image conversion could not read the source image.', [
                'path' => $sourcePath,
            ]);

            return false;
        }

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            Log::warning('Responsive image conversion is unavailable because GD WebP support is missing.', [
                'path' => $sourcePath,
            ]);

            return false;
        }

        $sourceBytes = $disk->get($sourcePath);
        $sourceImage = @imagecreatefromstring($sourceBytes);

        if ($sourceImage === false) {
            Log::warning('Responsive image conversion failed to decode the source image.', [
                'path' => $sourcePath,
            ]);

            return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $width = $targetWidth;
        $height = max(1, (int) round($sourceHeight * ($width / $sourceWidth)));
        $variantImage = imagecreatetruecolor($width, $height);

        if ($variantImage === false) {
            imagedestroy($sourceImage);

            Log::warning('Responsive image conversion could not allocate the output image.', [
                'path' => $sourcePath,
            ]);

            return false;
        }

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

            return false;
        }

        if (! $disk->put($variantPath, $webpBytes)) {
            Log::warning('Responsive WebP image could not be cached.', [
                'path' => $sourcePath,
            ]);

            return false;
        }

        return true;
    }
}
