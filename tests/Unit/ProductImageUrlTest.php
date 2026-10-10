<?php

namespace Tests\Unit;

use App\Models\HomepageSlide;
use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductImageUrlTest extends TestCase
{
    public function test_uploaded_image_urls_use_the_current_host(): void
    {
        $product = new Product;
        $slide = new HomepageSlide(['image_path' => 'products/slides/hero.jpg']);

        $this->assertSame('/storage/products/catalog/design.jpg', $product->imageUrl('products/catalog/design.jpg'));
        $this->assertSame('/storage/products/slides/hero.jpg', $slide->imageUrl());
        $this->assertSame('https://images.example.com/design.jpg', $product->imageUrl('https://images.example.com/design.jpg'));
    }
}
