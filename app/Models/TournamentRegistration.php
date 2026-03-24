<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentRegistration extends Model
{
    use HasFactory;

    public const STATUS_REGISTERED = 'registered';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_DROPPED = 'dropped';
    public const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'tournament_id',
        'user_id',
        'status',
        'points',
        'wins',
        'draws',
        'losses',
        'bye_rounds',
        'opponent_win_rate',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'int',
            'wins' => 'int',
            'draws' => 'int',
            'losses' => 'int',
            'bye_rounds' => 'int',
            'opponent_win_rate' => 'decimal:2',
            'checked_in_at' => 'datetime',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matchesAsPlayerOne(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'player_one_registration_id');
    }

    public function matchesAsPlayerTwo(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'player_two_registration_id');
    }
}
