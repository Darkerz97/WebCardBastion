<?php

namespace App\Models;

use App\Models\Concerns\HasSyncVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory, HasSyncVersion;

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';
    public const DIRECTION_ADJUSTMENT = 'adjustment';

    public const TYPE_SALE = 'sale';
    public const TYPE_RESTOCK = 'restock';
    public const TYPE_MANUAL_ADJUSTMENT = 'manual_adjustment';
    public const TYPE_RETURN = 'return';
    public const TYPE_SYNC_CORRECTION = 'sync_correction';

    public const SOURCE_SERVER = 'server';
    public const SOURCE_POS = 'pos';
    public const SOURCE_SYSTEM = 'system';

    protected $fillable = [
        'uuid',
        'product_id',
        'sale_id',
        'device_id',
        'user_id',
        'movement_type',
        'direction',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_cost',
        'reference',
        'notes',
        'source',
        'occurred_at',
        'sync_version',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'int',
            'stock_before' => 'int',
            'stock_after' => 'int',
            'unit_cost' => 'decimal:2',
            'occurred_at' => 'datetime',
            'sync_version' => 'int',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['product_id'] ?? null, fn (Builder $builder, $productId) => $builder->where('product_id', $productId))
            ->when($filters['device_id'] ?? null, fn (Builder $builder, $deviceId) => $builder->where('device_id', $deviceId))
            ->when($filters['movement_type'] ?? null, fn (Builder $builder, $movementType) => $builder->where('movement_type', $movementType))
            ->when($filters['source'] ?? null, fn (Builder $builder, $source) => $builder->where('source', $source))
            ->when($filters['date_from'] ?? null, fn (Builder $builder, $dateFrom) => $builder->whereDate('occurred_at', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn (Builder $builder, $dateTo) => $builder->whereDate('occurred_at', '<=', $dateTo));
    }
}
