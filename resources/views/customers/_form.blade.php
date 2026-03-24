<div class="grid grid-2">
    <div class="field"><label for="name">Nombre</label><input id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required></div>
    <div class="field"><label for="phone">Teléfono</label><input id="phone" type="text" name="phone" value="{{ old('phone', $customer->phone) }}"></div>
    <div class="field"><label for="email">Correo</label><input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}"></div>
    <div class="field"><label for="credit_balance">Saldo a favor</label><input id="credit_balance" type="number" step="0.01" min="0" name="credit_balance" value="{{ old('credit_balance', $customer->credit_balance ?? 0) }}"></div>
</div>
<div class="field"><label for="notes">Notas</label><textarea id="notes" name="notes" rows="4">{{ old('notes', $customer->notes) }}</textarea></div>
<div class="field">
    <label for="active">Activo</label>
    <select id="active" name="active" required>
        <option value="1" @selected(old('active', $customer->active ?? true))>Sí</option>
        <option value="0" @selected(! old('active', $customer->active ?? true))>No</option>
    </select>
</div>
<div class="actions"><button class="btn" type="submit">Guardar cliente</button><a class="btn secondary" href="{{ route('customers.index') }}">Cancelar</a></div>
