@extends('layouts.app', ['title' => 'Torneos', 'heading' => 'Torneos', 'subheading' => 'Gestiona eventos, rondas y seguimiento competitivo desde el panel administrativo.'])

@section('content')
    <div class="space-y-6">
        <div class="panel">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_auto] xl:w-[540px]">
                    <div class="field">
                        <label for="search">Buscar torneo</label>
                        <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre del torneo">
                    </div>
                    <button class="btn btn-secondary self-end" type="submit">Filtrar</button>
                </form>
                <a class="btn btn-primary" href="{{ route('tournaments.create') }}">Nuevo torneo</a>
            </div>
        </div>

        <div class="table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Torneo</th>
                        <th>Formato</th>
                        <th>Estado</th>
                        <th>Inicio</th>
                        <th>Publicado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tournaments as $tournament)
                        <tr>
                            <td>
                                <a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('tournaments.show', $tournament) }}">{{ $tournament->name }}</a>
                                <p class="mt-1 text-sm text-stone-500">{{ $tournament->slug }}</p>
                            </td>
                            <td>{{ strtoupper($tournament->format) }}</td>
                            <td><span class="badge">{{ str_replace('_', ' ', $tournament->status) }}</span></td>
                            <td>{{ optional($tournament->starts_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}</td>
                            <td>{{ $tournament->published ? 'Si' : 'No' }}</td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a class="btn btn-secondary" href="{{ route('tournaments.edit', $tournament) }}">Editar</a>
                                    <a class="btn btn-primary" href="{{ route('tournaments.show', $tournament) }}">Ver detalle</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-stone-500">No hay torneos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $tournaments->links() }}</div>
    </div>
@endsection
