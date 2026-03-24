<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

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
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'active' => 'boolean',
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
