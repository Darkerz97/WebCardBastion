<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentRegistration;
use App\Models\TournamentRound;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TournamentService
{
    public function registerPlayer(Tournament $tournament, int $userId): TournamentRegistration
    {
        if ($tournament->max_players && $tournament->registrations()->count() >= $tournament->max_players) {
            throw new InvalidArgumentException('El torneo ya alcanzo su limite de jugadores.');
        }

        return TournamentRegistration::query()->firstOrCreate(
            ['tournament_id' => $tournament->id, 'user_id' => $userId],
            ['status' => TournamentRegistration::STATUS_REGISTERED],
        );
    }

    public function generateNextRound(Tournament $tournament): TournamentRound
    {
        return DB::transaction(function () use ($tournament): TournamentRound {
            if ($tournament->rounds()->where('status', '!=', TournamentRound::STATUS_COMPLETED)->exists()) {
                throw new InvalidArgumentException('Primero completa la ronda activa antes de generar una nueva.');
            }

            $roundNumber = ((int) $tournament->rounds()->max('round_number')) + 1;

            if ($roundNumber > $tournament->rounds_count) {
                throw new InvalidArgumentException('El torneo ya alcanzo el numero maximo de rondas.');
            }

            $registrations = $tournament->registrations()
                ->whereIn('status', [
                    TournamentRegistration::STATUS_REGISTERED,
                    TournamentRegistration::STATUS_CHECKED_IN,
                    TournamentRegistration::STATUS_FINISHED,
                ])
                ->with('user')
                ->orderByDesc('points')
                ->orderByDesc('wins')
                ->orderBy('id')
                ->get()
                ->values();

            if ($registrations->count() < 2) {
                throw new InvalidArgumentException('Se necesitan al menos dos jugadores para generar una ronda.');
            }

            $round = $tournament->rounds()->create([
                'round_number' => $roundNumber,
                'status' => TournamentRound::STATUS_PAIRINGS_READY,
                'starts_at' => now(),
            ]);

            $table = 1;

            while ($registrations->isNotEmpty()) {
                /** @var TournamentRegistration $playerOne */
                $playerOne = $registrations->shift();
                /** @var TournamentRegistration|null $playerTwo */
                $playerTwo = $registrations->shift();

                if (! $playerTwo) {
                    $round->matches()->create([
                        'tournament_id' => $tournament->id,
                        'player_one_registration_id' => $playerOne->id,
                        'player_two_registration_id' => null,
                        'table_number' => $table++,
                        'player_one_score' => 2,
                        'player_two_score' => 0,
                        'winner_registration_id' => $playerOne->id,
                        'is_draw' => false,
                        'is_bye' => true,
                        'status' => TournamentMatch::STATUS_CONFIRMED,
                        'reported_at' => now(),
                    ]);
                    continue;
                }

                $round->matches()->create([
                    'tournament_id' => $tournament->id,
                    'player_one_registration_id' => $playerOne->id,
                    'player_two_registration_id' => $playerTwo->id,
                    'table_number' => $table++,
                    'status' => TournamentMatch::STATUS_PENDING,
                ]);
            }

            $this->syncStandings($tournament);
            $this->refreshRoundAndTournamentStatus($round->fresh());
            $tournament->update(['status' => Tournament::STATUS_IN_PROGRESS]);

            return $round->load(['matches.playerOneRegistration.user', 'matches.playerTwoRegistration.user']);
        });
    }

    public function reportMatchResult(TournamentMatch $match, int $playerOneScore, int $playerTwoScore): TournamentMatch
    {
        return DB::transaction(function () use ($match, $playerOneScore, $playerTwoScore): TournamentMatch {
            if ($match->status === TournamentMatch::STATUS_CONFIRMED) {
                throw new InvalidArgumentException('Este resultado ya fue confirmado.');
            }

            $match->update([
                'player_one_score' => $playerOneScore,
                'player_two_score' => $playerTwoScore,
                'winner_registration_id' => $playerOneScore === $playerTwoScore
                    ? null
                    : ($playerOneScore > $playerTwoScore ? $match->player_one_registration_id : $match->player_two_registration_id),
                'is_draw' => $playerOneScore === $playerTwoScore,
                'is_bye' => false,
                'status' => TournamentMatch::STATUS_CONFIRMED,
                'reported_at' => now(),
            ]);

            $this->syncStandings($match->tournament()->firstOrFail());
            $this->refreshRoundAndTournamentStatus($match->round()->firstOrFail());

            return $match->fresh(['playerOneRegistration.user', 'playerTwoRegistration.user', 'winnerRegistration.user']);
        });
    }

    public function standings(Tournament $tournament): Collection
    {
        return $tournament->registrations()
            ->with('user')
            ->orderByDesc('points')
            ->orderByDesc('wins')
            ->orderByDesc('opponent_win_rate')
            ->orderBy('id')
            ->get();
    }

    private function syncStandings(Tournament $tournament): void
    {
        $registrations = $tournament->registrations()->get()->keyBy('id');

        foreach ($registrations as $registration) {
            $registration->update([
                'points' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'bye_rounds' => 0,
                'opponent_win_rate' => 0,
            ]);
        }

        $matches = $tournament->matches()
            ->where('status', TournamentMatch::STATUS_CONFIRMED)
            ->get();

        foreach ($matches as $match) {
            $playerOne = $registrations->get($match->player_one_registration_id);
            $playerTwo = $registrations->get($match->player_two_registration_id);

            if (! $playerOne) {
                continue;
            }

            if ($match->is_bye) {
                $playerOne->update([
                    'points' => $playerOne->points + 3,
                    'wins' => $playerOne->wins + 1,
                    'bye_rounds' => $playerOne->bye_rounds + 1,
                ]);

                $registrations->put($playerOne->id, $playerOne->fresh());

                continue;
            }

            if ($match->is_draw) {
                $playerOne->update([
                    'points' => $playerOne->points + 1,
                    'draws' => $playerOne->draws + 1,
                ]);

                if ($playerTwo) {
                    $playerTwo->update([
                        'points' => $playerTwo->points + 1,
                        'draws' => $playerTwo->draws + 1,
                    ]);
                    $registrations->put($playerTwo->id, $playerTwo->fresh());
                }

                $registrations->put($playerOne->id, $playerOne->fresh());

                continue;
            }

            if ($match->winner_registration_id === $playerOne->id) {
                $playerOne->update([
                    'points' => $playerOne->points + 3,
                    'wins' => $playerOne->wins + 1,
                ]);

                if ($playerTwo) {
                    $playerTwo->update([
                        'losses' => $playerTwo->losses + 1,
                    ]);
                    $registrations->put($playerTwo->id, $playerTwo->fresh());
                }

                $registrations->put($playerOne->id, $playerOne->fresh());

                continue;
            }

            if ($playerTwo) {
                $playerTwo->update([
                    'points' => $playerTwo->points + 3,
                    'wins' => $playerTwo->wins + 1,
                ]);

                $registrations->put($playerTwo->id, $playerTwo->fresh());
            }

            $playerOne->update([
                'losses' => $playerOne->losses + 1,
            ]);
            $registrations->put($playerOne->id, $playerOne->fresh());
        }

        foreach ($registrations as $registration) {
            $opponents = $matches
                ->filter(function (TournamentMatch $match) use ($registration): bool {
                    return $match->player_one_registration_id === $registration->id
                        || $match->player_two_registration_id === $registration->id;
                })
                ->flatMap(function (TournamentMatch $match) use ($registration): array {
                    return array_filter([
                        $match->player_one_registration_id === $registration->id ? $match->player_two_registration_id : $match->player_one_registration_id,
                    ]);
                })
                ->unique()
                ->values();

            $rates = $opponents
                ->map(function (int $opponentId) use ($registrations): float {
                    $opponent = $registrations->get($opponentId);

                    if (! $opponent) {
                        return 0;
                    }

                    $games = $opponent->wins + $opponent->draws + $opponent->losses;

                    return $games > 0 ? round($opponent->wins / $games, 4) : 0;
                });

            $registration->update([
                'opponent_win_rate' => $rates->isNotEmpty() ? round($rates->avg() * 100, 2) : 0,
            ]);
        }
    }

    private function refreshRoundAndTournamentStatus(TournamentRound $round): void
    {
        if ($round->matches()->where('status', '!=', TournamentMatch::STATUS_CONFIRMED)->exists()) {
            return;
        }

        $round->update([
            'status' => TournamentRound::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $tournament = $round->tournament;

        if ($tournament->rounds()->where('status', '!=', TournamentRound::STATUS_COMPLETED)->doesntExist()
            && $tournament->rounds()->count() >= $tournament->rounds_count) {
            $tournament->update(['status' => Tournament::STATUS_COMPLETED]);
        }
    }
}
