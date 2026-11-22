<?php

namespace App\Console\Commands;

use App\Models\HomepageSlide;
use App\Models\Product;
use App\Services\ResponsiveImageCache;
use Illuminate\Console\Command;

class CacheResponsiveImages extends Command
{
    protected $signature = 'images:cache';

    protected $description = 'Pre-generate responsive WebP variants for product and slide images.';

    public function handle(): int
    {
        $imagePaths = collect();

        foreach (Product::query()->select(['media_paths', 'image_urls'])->cursor() as $product) {
            $imagePaths = $imagePaths->merge($product->images());
        }

        foreach (HomepageSlide::query()->select('image_path')->cursor() as $slide) {
            if ($slide->image_path) {
                $imagePaths->push($slide->image_path);
            }
        }

        $imagePaths = $imagePaths
            ->filter(fn (string $image): bool => ResponsiveImageCache::sourcePath($image) !== null)
            ->map(fn (string $image): string => ResponsiveImageCache::sourcePath($image))
            ->unique()
            ->values();

        $this->output->progressStart($imagePaths->count());
        $failedVariants = 0;

        $imagePaths->each(function (string $sourcePath) use (&$failedVariants): void {
            foreach (ResponsiveImageCache::WIDTHS as $width) {
                if (! ResponsiveImageCache::createVariant($sourcePath, $width)) {
                    $failedVariants++;
                }
            }

            $this->output->progressAdvance();
        });

        $this->output->progressFinish();

        if ($failedVariants > 0) {
            $this->components->error("Failed to generate {$failedVariants} responsive image variants.");

            return self::FAILURE;
        }

        $this->components->info("Responsive image variants are ready for {$imagePaths->count()} source images.");

        return self::SUCCESS;
    }
}
