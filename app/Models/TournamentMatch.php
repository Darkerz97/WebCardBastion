<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMatch extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_REPORTED = 'reported';
    public const STATUS_CONFIRMED = 'confirmed';

    protected $fillable = [
        'tournament_round_id',
        'tournament_id',
        'player_one_registration_id',
        'player_two_registration_id',
        'table_number',
        'player_one_score',
        'player_two_score',
        'winner_registration_id',
        'is_draw',
        'is_bye',
        'status',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'table_number' => 'int',
            'player_one_score' => 'int',
            'player_two_score' => 'int',
            'is_draw' => 'boolean',
            'is_bye' => 'boolean',
            'reported_at' => 'datetime',
        ];
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(TournamentRound::class, 'tournament_round_id');
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function playerOneRegistration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'player_one_registration_id');
    }

    public function playerTwoRegistration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'player_two_registration_id');
    }

    public function winnerRegistration(): BelongsTo
    {
        return $this->belongsTo(TournamentRegistration::class, 'winner_registration_id');
    }
}
