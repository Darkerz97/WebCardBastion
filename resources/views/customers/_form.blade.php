<div class="grid grid-2">
    <div class="field"><label for="name">Nombre</label><input id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required></div>
    <div class="field">
        <label for="user_id">Cuenta vinculada</label>
        <select id="user_id" name="user_id">
            <option value="">Sin vincular</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $customer->user_id) === (string) $user->id)>{{ $user->name }}{{ $user->role ? ' · '.$user->role->code : '' }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label for="phone">Telefono</label><input id="phone" type="text" name="phone" value="{{ old('phone', $customer->phone) }}"></div>
    <div class="field"><label for="email">Correo</label><input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}"></div>
    <div class="field"><label for="credit_balance">Saldo a favor</label><input id="credit_balance" type="number" step="0.01" min="0" name="credit_balance" value="{{ old('credit_balance', $customer->credit_balance ?? 0) }}"></div>
</div>
<div class="field"><label for="notes">Notas</label><textarea id="notes" name="notes" rows="4">{{ old('notes', $customer->notes) }}</textarea></div>
<div class="field">
    <label for="active">Activo</label>
    <select id="active" name="active" required>
        <option value="1" @selected(old('active', $customer->active ?? true))>Si</option>
        <option value="0" @selected((string) old('active', (int) ($customer->active ?? true)) === '0')>No</option>
    </select>
</div>
<div class="actions"><button class="btn" type="submit">Guardar cliente</button><a class="btn secondary" href="{{ route('customers.index') }}">Cancelar</a></div>
