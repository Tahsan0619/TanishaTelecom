<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'subtitle', 'image', 'link', 'position', 'sort_order', 'is_active'])]
class Slider extends Model
{
    public const POSITIONS = [
        'main' => 'Main slider (large, left)',
        'side' => 'Side image (small, right)',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        return Media::url($this->image);
    }
}
