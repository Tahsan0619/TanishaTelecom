<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id', 'name', 'slug', 'short_description', 'description', 'price',
    'sale_price', 'stock', 'unit', 'image', 'is_featured', 'is_active', 'free_shipping', 'sold_count',
])]
class Product extends Model
{
    /** SQL expression for the price a customer actually pays. */
    public const FINAL_PRICE_SQL = 'COALESCE(NULLIF(sale_price, 0), price)';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'sold_count' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'free_shipping' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getOnSaleAttribute(): bool
    {
        return $this->sale_price !== null
            && (float) $this->sale_price > 0
            && (float) $this->sale_price < (float) $this->price;
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->on_sale ? (float) $this->sale_price : (float) $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->on_sale || (float) $this->price <= 0) {
            return 0;
        }

        return (int) round((1 - (float) $this->sale_price / (float) $this->price) * 100);
    }

    public function getImageUrlAttribute(): ?string
    {
        return Media::url($this->image);
    }

    public function toApi(bool $full = false): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'sale_price' => $this->on_sale ? (float) $this->sale_price : null,
            'final_price' => $this->final_price,
            'on_sale' => $this->on_sale,
            'discount_percent' => $this->discount_percent,
            'stock' => $this->stock,
            'in_stock' => $this->stock > 0,
            'unit' => $this->unit,
            'image_url' => $this->image_url,
            'is_featured' => $this->is_featured,
            'free_shipping' => $this->free_shipping,
        ];

        if ($full) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
