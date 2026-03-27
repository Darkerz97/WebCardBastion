<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TournamentMatch;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Models\User;
use App\Services\TournamentService;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class PlayerTournamentController extends Controller
{
    public function __construct(private readonly TournamentService $tournamentService)
    {
    }

    public function index(): View
    {
        /** @var User $user */
        $user = request()->user();

        $registrations = $user
            ->tournamentRegistrations()
            ->with('tournament')
            ->get()
            ->sortByDesc(fn (TournamentRegistration $registration) => $registration->tournament?->starts_at ?? $registration->created_at)
            ->values();

        $registrationMap = $registrations->keyBy('tournament_id');
        $registrationIds = $registrations->pluck('id');
        $confirmedMatches = $this->loadConfirmedMatches($registrationIds);
        $attendedRegistrations = $registrations
            ->filter(fn (TournamentRegistration $registration) => $this->hasAttendedTournament($registration))
            ->values();

        return view('account.tournaments', [
            'tournamentStats' => $this->buildTournamentStats($registrations, $confirmedMatches, $registrationIds),
            'attendedTournaments' => $attendedRegistrations,
            'tournaments' => Tournament::query()->published()->latest('starts_at')->paginate(10),
            'myRegistrations' => $registrationMap,
        ]);
    }

    public function store(Tournament $tournament): RedirectResponse
    {
        try {
            $this->tournamentService->registerPlayer($tournament, request()->user()->id);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['registration' => $exception->getMessage()]);
        }

        return back()->with('success', 'Inscripcion registrada correctamente.');
    }

    private function loadConfirmedMatches(Collection $registrationIds): Collection
    {
        if ($registrationIds->isEmpty()) {
            return collect();
        }

        return TournamentMatch::query()
            ->with(['round', 'tournament'])
            ->where('status', TournamentMatch::STATUS_CONFIRMED)
            ->where(function ($query) use ($registrationIds): void {
                $query->whereIn('player_one_registration_id', $registrationIds)
                    ->orWhereIn('player_two_registration_id', $registrationIds);
            })
            ->get()
            ->sortBy(function (TournamentMatch $match): string {
                return sprintf(
                    '%s-%05d-%05d',
                    optional($match->reported_at ?? $match->round?->completed_at ?? $match->tournament?->starts_at)->format('YmdHis') ?? '00000000000000',
                    $match->round?->round_number ?? 0,
                    $match->id,
                );
            })
            ->values();
    }

    private function buildTournamentStats(Collection $registrations, Collection $confirmedMatches, Collection $registrationIds): array
    {
        $attendedRegistrations = $registrations->filter(
            fn (TournamentRegistration $registration) => $this->hasAttendedTournament($registration),
        );

        $wins = (int) $attendedRegistrations->sum('wins');
        $losses = (int) $attendedRegistrations->sum('losses');
        $draws = (int) $attendedRegistrations->sum('draws');
        $decisiveMatches = $wins + $losses;

        return [
            'attended' => $attendedRegistrations->count(),
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'win_streak' => $this->calculateCurrentWinStreak($confirmedMatches, $registrationIds),
            'wl_rate' => $decisiveMatches > 0 ? number_format(($wins / $decisiveMatches) * 100, 1).'%' : '0%',
        ];
    }

    private function calculateCurrentWinStreak(Collection $confirmedMatches, Collection $registrationIds): int
    {
        $streak = 0;

        foreach ($confirmedMatches as $match) {
            $result = $this->resolveMatchResult($match, $registrationIds);

            if ($result === 'win') {
                $streak++;
                continue;
            }

            $streak = 0;
        }

        return $streak;
    }

    private function resolveMatchResult(TournamentMatch $match, Collection $registrationIds): string
    {
        $registrationId = $registrationIds->first(
            fn (int $id) => in_array($id, array_filter([$match->player_one_registration_id, $match->player_two_registration_id]), true),
        );

        if (! $registrationId) {
            return 'unknown';
        }

        if ($match->is_draw) {
            return 'draw';
        }

        return $match->winner_registration_id === $registrationId ? 'win' : 'loss';
    }

    private function hasAttendedTournament(TournamentRegistration $registration): bool
    {
        return $registration->checked_in_at !== null
            || in_array($registration->status, [
                TournamentRegistration::STATUS_CHECKED_IN,
                TournamentRegistration::STATUS_FINISHED,
                TournamentRegistration::STATUS_DROPPED,
            ], true)
            || ($registration->wins + $registration->draws + $registration->losses + $registration->bye_rounds) > 0;
    }
}
