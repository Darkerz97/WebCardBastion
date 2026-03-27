@extends('layouts.app', ['title' => $tournament->name, 'heading' => $tournament->name, 'subheading' => 'Control de registros, standings y resultados del torneo.'])

@section('content')
    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="panel">
                <div class="flex flex-wrap gap-2">
                    <span class="badge">{{ str_replace('_', ' ', $tournament->status) }}</span>
                    @if ($tournament->published)
                        <span class="badge">Publicado</span>
                    @endif
                </div>

                <dl class="mt-6 grid gap-4 text-sm">
                    <div><dt class="font-semibold text-stone-500">Formato</dt><dd class="mt-1 text-stone-900">{{ strtoupper($tournament->format) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Entrada</dt><dd class="mt-1 text-stone-900">${{ number_format($tournament->entry_fee, 2) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Maximo de jugadores</dt><dd class="mt-1 text-stone-900">{{ $tournament->max_players ?: 'Sin limite' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Rondas</dt><dd class="mt-1 text-stone-900">{{ $tournament->rounds_count }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Inicio</dt><dd class="mt-1 text-stone-900">{{ optional($tournament->starts_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Cierre de registro</dt><dd class="mt-1 text-stone-900">{{ optional($tournament->registration_closes_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Descripcion</dt><dd class="mt-1 whitespace-pre-line text-stone-900">{{ $tournament->description ?: 'Sin descripcion' }}</dd></div>
                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a class="btn btn-secondary" href="{{ route('tournaments.edit', $tournament) }}">Editar torneo</a>
                    <form method="POST" action="{{ route('tournaments.rounds.store', $tournament) }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Generar siguiente ronda</button>
                    </form>
                </div>
            </div>

            <div class="panel">
                <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Standings</h2>
                <div class="mt-5 overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Jugador</th>
                                <th>Puntos</th>
                                <th>W</th>
                                <th>D</th>
                                <th>L</th>
                                <th>OMW</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($standings as $registration)
                                <tr>
                                    <td>{{ $registration->user?->name ?? 'Jugador' }}</td>
                                    <td>{{ $registration->points }}</td>
                                    <td>{{ $registration->wins }}</td>
                                    <td>{{ $registration->draws }}</td>
                                    <td>{{ $registration->losses }}</td>
                                    <td>{{ number_format($registration->opponent_win_rate, 2) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-stone-500">Aun no hay jugadores registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel">
            <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Jugadores registrados</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th>Estado</th>
                            <th>Puntos</th>
                            <th>Record</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tournament->registrations as $registration)
                            <tr>
                                <td>{{ $registration->user?->name ?? 'Jugador' }}</td>
                                <td><span class="badge">{{ str_replace('_', ' ', $registration->status) }}</span></td>
                                <td>{{ $registration->points }}</td>
                                <td>{{ $registration->wins }}W / {{ $registration->draws }}D / {{ $registration->losses }}L</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-stone-500">No hay registros para este torneo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            @forelse ($tournament->rounds as $round)
                <div class="panel">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="section-kicker">Ronda {{ $round->round_number }}</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">{{ str_replace('_', ' ', $round->status) }}</h2>
                        </div>
                        <span class="badge">{{ $round->matches->count() }} partidas</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @foreach ($round->matches as $match)
                            <div class="rounded-2xl border border-stone-200 px-4 py-4">
                                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                    <div>
                                        <p class="font-semibold text-stone-900">Mesa {{ $match->table_number ?: '-' }}</p>
                                        <p class="mt-2 text-sm text-stone-600">
                                            {{ $match->playerOneRegistration?->user?->name ?? 'Bye' }}
                                            vs
                                            {{ $match->playerTwoRegistration?->user?->name ?? 'Bye' }}
                                        </p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-stone-500">{{ $match->status }}</p>
                                    </div>

                                    @if (! $match->is_bye && $match->status !== \App\Models\TournamentMatch::STATUS_CONFIRMED)
                                        <form method="POST" action="{{ route('tournaments.matches.report', $match) }}" class="grid gap-3 sm:grid-cols-[120px_120px_auto]">
                                            @csrf
                                            <div class="field">
                                                <label for="player_one_score_{{ $match->id }}">P1</label>
                                                <input id="player_one_score_{{ $match->id }}" type="number" min="0" name="player_one_score" value="{{ $match->player_one_score }}">
                                            </div>
                                            <div class="field">
                                                <label for="player_two_score_{{ $match->id }}">P2</label>
                                                <input id="player_two_score_{{ $match->id }}" type="number" min="0" name="player_two_score" value="{{ $match->player_two_score }}">
                                            </div>
                                            <button class="btn btn-primary self-end" type="submit">Reportar</button>
                                        </form>
                                    @else
                                        <div class="text-sm text-stone-600">
                                            <span class="badge">{{ $match->player_one_score }} - {{ $match->player_two_score }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="panel">
                    <p class="text-sm text-stone-500">Aun no hay rondas generadas para este torneo.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
