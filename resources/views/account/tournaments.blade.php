@extends('layouts.public', ['title' => 'Mis torneos | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="panel">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Portal del jugador</p>
            <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">Mis torneos</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">
                Revisa en cuántos torneos has participado, tu racha actual de victorias y tu porcentaje de ganados frente a perdidos.
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Asistidos</p>
                    <p class="mt-3 text-3xl font-black text-stone-900">{{ $tournamentStats['attended'] }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Win streak</p>
                    <p class="mt-3 text-3xl font-black text-stone-900">{{ $tournamentStats['win_streak'] }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">W/L rate</p>
                    <p class="mt-3 text-3xl font-black text-stone-900">{{ $tournamentStats['wl_rate'] }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Victorias</p>
                    <p class="mt-3 text-3xl font-black text-stone-900">{{ $tournamentStats['wins'] }}</p>
                </article>
                <article class="metric-card">
                    <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Derrotas</p>
                    <p class="mt-3 text-3xl font-black text-stone-900">{{ $tournamentStats['losses'] }}</p>
                    <p class="mt-2 text-sm text-stone-500">Empates: {{ $tournamentStats['draws'] }}</p>
                </article>
            </div>
        </div>

        <div class="panel mt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Torneos asistidos</h2>
                    <p class="mt-2 text-sm text-stone-500">Historial de eventos en los que ya participaste como jugador.</p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($attendedTournaments as $registration)
                    <div class="rounded-2xl border border-stone-200 px-4 py-4">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $registration->tournament?->name ?? 'Torneo' }}</p>
                                <p class="mt-1 text-sm text-stone-500">
                                    {{ optional($registration->tournament?->starts_at)->format('d/m/Y H:i') ?: 'Fecha por confirmar' }}
                                    · {{ $registration->tournament?->format ?: 'Formato pendiente' }}
                                </p>
                                <p class="mt-2 text-sm text-stone-600">
                                    Estado {{ str_replace('_', ' ', $registration->status) }}
                                </p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Record</p>
                                    <p class="mt-2 text-lg font-black text-stone-900">{{ $registration->wins }}W / {{ $registration->draws }}D / {{ $registration->losses }}L</p>
                                </div>
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Puntos</p>
                                    <p class="mt-2 text-lg font-black text-stone-900">{{ $registration->points }}</p>
                                </div>
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.22em] text-stone-500">OMW</p>
                                    <p class="mt-2 text-lg font-black text-stone-900">{{ number_format((float) $registration->opponent_win_rate, 2) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-stone-500">Aun no tienes torneos asistidos. Cuando participes en eventos con check-in o resultados registrados, apareceran aqui.</p>
                @endforelse
            </div>
        </div>

        <div class="panel mt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Torneos publicados</h2>
                    <p class="mt-2 text-sm text-stone-500">Explora los eventos abiertos y registra tu participacion desde esta misma cuenta.</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse ($tournaments as $tournament)
                    @php($registration = $myRegistrations->get($tournament->id))

                    <article class="rounded-2xl border border-stone-200 px-5 py-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $tournament->name }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ $tournament->format ?: 'Formato pendiente' }}</p>
                            </div>
                            @if ($registration)
                                <span class="badge">{{ str_replace('_', ' ', $registration->status) }}</span>
                            @else
                                <span class="badge">Disponible</span>
                            @endif
                        </div>

                        <p class="mt-4 text-sm leading-7 text-stone-600">{{ $tournament->description ?: 'Evento publicado en Card Bastion.' }}</p>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Inicio</p>
                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ optional($tournament->starts_at)->format('d/m/Y H:i') ?: 'Por confirmar' }}</p>
                            </div>
                            <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Entrada</p>
                                <p class="mt-2 text-sm font-semibold text-stone-900">${{ number_format((float) $tournament->entry_fee, 2) }}</p>
                            </div>
                            <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                                <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Cupos</p>
                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ $tournament->max_players ?: 'Abierto' }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            @if ($registration)
                                <p class="text-sm text-stone-500">Ya cuentas con registro en este torneo.</p>
                            @else
                                <form method="POST" action="{{ route('account.tournaments.store', $tournament) }}">
                                    @csrf
                                    <button class="btn btn-primary w-full" type="submit">Inscribirme</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-stone-500">Por ahora no hay torneos publicados.</p>
                @endforelse
            </div>

            <div class="mt-6">{{ $tournaments->links() }}</div>
        </div>
    </section>
@endsection
