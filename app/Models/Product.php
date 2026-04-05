<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'sale_price',
        'stock',
        'images',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'images'      => 'array',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'price'       => 'decimal:2',
            'sale_price'  => 'decimal:2',
        ];
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Computed helpers
    public function effectivePrice(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function discountPercent(): int
    {
        if (!$this->isOnSale()) return 0;
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function primaryImage(): ?string
    {
        $images = $this->images ?? [];
        if (empty($images)) return null;
        $first = $images[0];
        // Support full URLs (Unsplash, CDN, etc.)
        if (str_starts_with($first, 'http://') || str_starts_with($first, 'https://')) {
            return $first;
        }
        return asset('storage/' . $first);
    }

    public function averageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function reviewCount(): int
    {
        return $this->reviews()->count();
    }
}
