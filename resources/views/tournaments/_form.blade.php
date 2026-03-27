<div class="grid gap-5 lg:grid-cols-2">
    <div class="field lg:col-span-2">
        <label for="name">Nombre del torneo</label>
        <input id="name" type="text" name="name" value="{{ old('name', $tournament->name) }}" required>
    </div>
    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $tournament->slug) }}" placeholder="Se genera automaticamente si lo dejas vacio">
    </div>
    <div class="field">
        <label for="format">Formato</label>
        <input id="format" type="text" name="format" value="{{ old('format', $tournament->format ?? 'swiss') }}" required>
    </div>
    <div class="field lg:col-span-2">
        <label for="description">Descripcion</label>
        <textarea id="description" name="description" rows="5">{{ old('description', $tournament->description) }}</textarea>
    </div>
    <div class="field">
        <label for="status">Estado</label>
        <select id="status" name="status" required>
            @foreach ([
                \App\Models\Tournament::STATUS_DRAFT => 'Borrador',
                \App\Models\Tournament::STATUS_REGISTRATION_OPEN => 'Registro abierto',
                \App\Models\Tournament::STATUS_IN_PROGRESS => 'En progreso',
                \App\Models\Tournament::STATUS_COMPLETED => 'Completado',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $tournament->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="published">Publicado</label>
        <select id="published" name="published" required>
            <option value="1" @selected(old('published', $tournament->published ?? true))>Si</option>
            <option value="0" @selected((string) old('published', (int) ($tournament->published ?? true)) === '0')>No</option>
        </select>
    </div>
    <div class="field">
        <label for="entry_fee">Costo de entrada</label>
        <input id="entry_fee" type="number" step="0.01" min="0" name="entry_fee" value="{{ old('entry_fee', $tournament->entry_fee ?? 0) }}">
    </div>
    <div class="field">
        <label for="max_players">Maximo de jugadores</label>
        <input id="max_players" type="number" min="2" name="max_players" value="{{ old('max_players', $tournament->max_players) }}">
    </div>
    <div class="field">
        <label for="rounds_count">Rondas</label>
        <input id="rounds_count" type="number" min="1" max="12" name="rounds_count" value="{{ old('rounds_count', $tournament->rounds_count ?? 3) }}" required>
    </div>
    <div class="field">
        <label for="starts_at">Fecha de inicio</label>
        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($tournament->starts_at)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="field">
        <label for="registration_closes_at">Cierre de registro</label>
        <input id="registration_closes_at" type="datetime-local" name="registration_closes_at" value="{{ old('registration_closes_at', optional($tournament->registration_closes_at)->format('Y-m-d\TH:i')) }}">
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="btn btn-primary" type="submit">Guardar torneo</button>
    <a class="btn btn-secondary" href="{{ route('tournaments.index') }}">Cancelar</a>
</div>
