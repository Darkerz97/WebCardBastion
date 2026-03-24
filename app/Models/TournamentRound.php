<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentRound extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAIRINGS_READY = 'pairings_ready';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'tournament_id',
        'round_number',
        'status',
        'starts_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'round_number' => 'int',
            'starts_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }
}
