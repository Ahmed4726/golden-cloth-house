<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_route_returns_cached_webp_variants_at_the_requested_width(): void
    {
        Storage::fake('public');
        $imagePath = UploadedFile::fake()
            ->image('catalog.jpg', 1200, 900)
            ->store('products', 'public');

        $response = $this->get(route('media.show', [
            'path' => $imagePath,
            'width' => 320,
        ]));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/webp')
            ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');

        $webpBytes = $response->streamedContent();
        $dimensions = getimagesizefromstring($webpBytes);
        $this->assertSame(320, $dimensions[0]);
        $this->assertSame(240, $dimensions[1]);
        $this->assertLessThan(Storage::disk('public')->size($imagePath), strlen($webpBytes));
    }

    public function test_media_route_rejects_unsupported_responsive_widths(): void
    {
        Storage::fake('public');
        $imagePath = UploadedFile::fake()
            ->image('catalog.jpg', 1200, 900)
            ->store('products', 'public');

        $this->get(route('media.show', [
            'path' => $imagePath,
            'width' => 777,
        ]))->assertNotFound();
    }
}
