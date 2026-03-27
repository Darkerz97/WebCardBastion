<?php

namespace App\Models;

use App\Support\PublicFileUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'sku',
        'barcode',
        'category_id',
        'description',
        'short_description',
        'category',
        'cost',
        'price',
        'stock',
        'image_path',
        'active',
        'featured',
        'publish_to_store',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'price' => 'decimal:2',
            'stock' => 'int',
            'active' => 'boolean',
            'featured' => 'boolean',
            'publish_to_store' => 'boolean',
        ];
    }

    public function categoryModel(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('name', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('active', true)->where('publish_to_store', true);
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $path = $this->image_path ?: $this->images->firstWhere('is_primary', true)?->path ?: $this->images->first()?->path;

        return $path ? static::resolveImageUrl($path) : null;
    }

    public static function resolveImageUrl(?string $path): ?string
    {
        return PublicFileUrl::fromPublicDisk($path);
    }
}
