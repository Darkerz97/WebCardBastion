<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\TournamentService;
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
        $registrations = request()->user()
            ->tournamentRegistrations()
            ->with('tournament')
            ->get()
            ->keyBy('tournament_id');

        return view('account.tournaments', [
            'tournaments' => Tournament::query()->published()->latest('starts_at')->paginate(10),
            'myRegistrations' => $registrations,
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
}
