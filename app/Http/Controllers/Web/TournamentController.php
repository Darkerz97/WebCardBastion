<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\TournamentMatchResultRequest;
use App\Http\Requests\Tournament\TournamentRequest;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Services\TournamentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;

class TournamentController extends Controller
{
    public function __construct(private readonly TournamentService $tournamentService)
    {
    }

    public function index(Request $request): View
    {
        $tournaments = Tournament::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->toString().'%'))
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('tournaments.index', compact('tournaments'));
    }

    public function create(): View
    {
        return view('tournaments.create', ['tournament' => new Tournament([
            'status' => Tournament::STATUS_DRAFT,
            'format' => 'swiss',
            'published' => true,
            'rounds_count' => 3,
        ])]);
    }

    public function store(TournamentRequest $request): RedirectResponse
    {
        Tournament::create([
            ...$request->validated(),
            'uuid' => (string) Str::uuid(),
            'slug' => $request->validated('slug') ?: Str::slug($request->validated('name')),
            'entry_fee' => $request->validated('entry_fee', 0),
        ]);

        return redirect()->route('tournaments.index')->with('success', 'Torneo creado correctamente.');
    }

    public function show(Tournament $tournament): View
    {
        $tournament->load([
            'rounds.matches.playerOneRegistration.user',
            'rounds.matches.playerTwoRegistration.user',
            'registrations.user',
        ]);

        return view('tournaments.show', [
            'tournament' => $tournament,
            'standings' => $this->tournamentService->standings($tournament),
        ]);
    }

    public function edit(Tournament $tournament): View
    {
        return view('tournaments.edit', compact('tournament'));
    }

    public function update(TournamentRequest $request, Tournament $tournament): RedirectResponse
    {
        $tournament->update([
            ...$request->validated(),
            'slug' => $request->validated('slug') ?: Str::slug($request->validated('name')),
            'entry_fee' => $request->validated('entry_fee', 0),
        ]);

        return redirect()->route('tournaments.index')->with('success', 'Torneo actualizado correctamente.');
    }

    public function generateRound(Tournament $tournament): RedirectResponse
    {
        try {
            $this->tournamentService->generateNextRound($tournament);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['tournament' => $exception->getMessage()]);
        }

        return back()->with('success', 'Ronda generada correctamente.');
    }

    public function reportMatch(TournamentMatchResultRequest $request, TournamentMatch $match): RedirectResponse
    {
        try {
            $this->tournamentService->reportMatchResult(
                $match,
                $request->integer('player_one_score'),
                $request->integer('player_two_score'),
            );
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['match' => $exception->getMessage()]);
        }

        return back()->with('success', 'Resultado reportado correctamente.');
    }
}
