<?php

namespace App\Models;

use App\Models\Concerns\HasSyncVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory, HasSyncVersion;

    public const CHANNEL_POS = 'pos';
    public const CHANNEL_STOREFRONT = 'storefront';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PARTIAL = 'partial';
    public const PAYMENT_STATUS_PAID = 'paid';

    protected $fillable = [
        'uuid',
        'customer_id',
        'user_id',
        'device_id',
        'sale_number',
        'order_channel',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
        'subtotal',
        'discount',
        'total',
        'status',
        'payment_status',
        'sold_at',
        'sync_version',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
            'sync_version' => 'int',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class)->latest('occurred_at')->latest('id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['customer_id'] ?? null, fn (Builder $builder, $customerId) => $builder->where('customer_id', $customerId))
            ->when($filters['user_id'] ?? null, fn (Builder $builder, $userId) => $builder->where('user_id', $userId))
            ->when($filters['device_id'] ?? null, fn (Builder $builder, $deviceId) => $builder->where('device_id', $deviceId))
            ->when($filters['status'] ?? null, fn (Builder $builder, $status) => $builder->where('status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $builder, $dateFrom) => $builder->whereDate('sold_at', '>=', $dateFrom))
            ->when($filters['date_to'] ?? null, fn (Builder $builder, $dateTo) => $builder->whereDate('sold_at', '<=', $dateTo));
    }

    public function getPaidAmountAttribute(): string
    {
        return number_format((float) $this->payments->sum('amount'), 2, '.', '');
    }
}
