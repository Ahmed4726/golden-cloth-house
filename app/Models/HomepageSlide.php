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

    public function imageUrl(?int $width = null): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if ($width !== null) {
            return route('media.show', [
                'path' => $this->image_path,
                'width' => $width,
            ]);
        }

        return '/storage/'.ltrim($this->image_path, '/');
    }
}
