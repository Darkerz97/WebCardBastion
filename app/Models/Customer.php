<?php

namespace App\Models;

use App\Models\Concerns\HasSyncVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasSyncVersion, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'phone',
        'email',
        'notes',
        'credit_balance',
        'active',
        'sync_version',
    ];

    protected function casts(): array
    {
        return [
            'credit_balance' => 'decimal:2',
            'active' => 'boolean',
            'sync_version' => 'int',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function preorders(): HasMany
    {
        return $this->hasMany(Preorder::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
