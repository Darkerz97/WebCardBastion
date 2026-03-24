<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_REGISTRATION_OPEN = 'registration_open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'format',
        'status',
        'entry_fee',
        'max_players',
        'rounds_count',
        'starts_at',
        'registration_closes_at',
        'published',
    ];

    protected function casts(): array
    {
        return [
            'entry_fee' => 'decimal:2',
            'max_players' => 'int',
            'rounds_count' => 'int',
            'starts_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'published' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(TournamentRound::class)->orderBy('round_number');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }
}
