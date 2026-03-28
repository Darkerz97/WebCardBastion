<?php

namespace App\Models;

use App\Models\Concerns\HasSyncVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Preorder extends Model
{
    use HasFactory, HasSyncVersion;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DELIVERED = 'delivered';

    public const SOURCE_SERVER = 'server';
    public const SOURCE_POS = 'pos';
    public const SOURCE_WEB = 'web';

    protected $fillable = [
        'uuid',
        'customer_id',
        'preorder_number',
        'status',
        'subtotal',
        'discount',
        'total',
        'amount_paid',
        'amount_due',
        'expected_release_date',
        'notes',
        'source',
        'sync_version',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'expected_release_date' => 'datetime',
            'sync_version' => 'int',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PreorderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PreorderPayment::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['customer_id'] ?? null, fn (Builder $builder, $customerId) => $builder->where('customer_id', $customerId))
            ->when($filters['status'] ?? null, fn (Builder $builder, $status) => $builder->where('status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $builder, $dateFrom) => $builder->whereDate('created_at', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn (Builder $builder, $dateTo) => $builder->whereDate('created_at', '<=', $dateTo));
    }
}
