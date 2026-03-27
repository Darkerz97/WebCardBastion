<?php

namespace App\Models;

use App\Models\Concerns\HasSyncVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory, HasSyncVersion;

    public const TYPE_POS = 'pos';
    public const TYPE_MOBILE = 'mobile';
    public const TYPE_ADMIN_PANEL = 'admin_panel';

    protected $fillable = [
        'uuid',
        'device_code',
        'name',
        'type',
        'last_seen_at',
        'active',
        'sync_version',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'active' => 'boolean',
            'sync_version' => 'int',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class)->latest('occurred_at')->latest('id');
    }

    public function cashClosures(): HasMany
    {
        return $this->hasMany(CashClosure::class)->latest('closed_at')->latest('id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('device_code', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%");
        });
    }
}
