<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Card Bastion</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: radial-gradient(circle at top, #f0ddc6, #e9e0d0 45%, #d8c7b3 100%); font-family: Georgia, "Times New Roman", serif; }
        .card { width: min(440px, calc(100vw - 32px)); background: rgba(255,252,246,.95); padding: 32px; border-radius: 24px; border: 1px solid #d7c6b4; box-shadow: 0 16px 40px rgba(53,33,15,.12); }
        h1 { margin-top: 0; } label { display:block; margin-bottom:6px; color:#6b594d; } input { width:100%; padding:11px 12px; border-radius:12px; border:1px solid #d7c6b4; margin-bottom:14px; font:inherit; }
        button { width:100%; padding:12px; border:0; border-radius:12px; background:#8a3d16; color:white; font:inherit; cursor:pointer; } .muted { color:#6b594d; } .error { color:#a4332b; margin-bottom:14px; } .success { color:#20744a; margin-bottom:14px; }
    </style>
</head>
<body>
<div class="card">
    <div class="muted">Servidor central</div>
    <h1>Card Bastion</h1>
    <p class="muted">Ingresa con tu usuario administrador o de operación.</p>
    @if (session('success')) <div class="success">{{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="error">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Correo</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        <label for="password">Contraseña</label>
        <input id="password" type="password" name="password" required>
        <button type="submit">Entrar al panel</button>
    </form>
</div>
</body>
</html>
