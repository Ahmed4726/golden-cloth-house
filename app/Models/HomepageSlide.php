<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'image_path',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }
}
