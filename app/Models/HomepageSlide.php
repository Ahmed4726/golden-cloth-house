<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

        return route('media.show', ['path' => ltrim($this->image_path, '/')]);
    }

    public function imageExists(): bool
    {
        return $this->image_path
            ? Storage::disk('public')->exists(ltrim($this->image_path, '/'))
            : false;
    }
}
