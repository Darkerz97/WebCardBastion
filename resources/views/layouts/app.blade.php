<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Card Bastion Server' }}</title>
    <style>
        :root { --bg:#f4f1ea; --panel:#fffdf8; --accent:#8a3d16; --text:#1f1f1f; --muted:#666; --border:#ddd2c5; --success:#20744a; --danger:#a4332b; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; background: linear-gradient(135deg, #efe7db 0%, #f8f4ed 55%, #e7dccb 100%); color: var(--text); }
        a { color: inherit; text-decoration: none; }
        .app { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .sidebar { background: #2b211d; color: #f8f1e6; padding: 24px; }
        .brand { font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }
        .subtitle { color: #d4c2ae; margin-bottom: 24px; }
        .nav-link { display: block; padding: 12px 14px; border-radius: 10px; margin-bottom: 8px; background: rgba(255,255,255,0.04); }
        .nav-link.active, .nav-link:hover { background: rgba(229,184,143,0.18); }
        .content { padding: 28px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 12px; }
        .panel { background: var(--panel); border: 1px solid var(--border); border-radius: 18px; padding: 20px; box-shadow: 0 8px 30px rgba(43,33,29,0.06); }
        .grid { display: grid; gap: 18px; }
        .grid-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .metric { padding: 18px; border-radius: 16px; background: linear-gradient(180deg, #fff8ef, #f6ede1); border: 1px solid var(--border); }
        .metric small { color: var(--muted); display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .08em; }
        .metric strong { font-size: 1.8rem; }
        .actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 10px 16px; border-radius: 10px; border: 1px solid transparent; background: var(--accent); color: white; cursor: pointer; }
        .btn.secondary { background: #f5ede4; color: var(--text); border-color: var(--border); }
        .btn.danger { background: var(--danger); }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid var(--border); vertical-align: top; }
        th { font-size: .85rem; text-transform: uppercase; color: var(--muted); letter-spacing: .08em; }
        form.inline { display: inline; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 10px; background: white; font: inherit; }
        label { font-size: .92rem; margin-bottom: 6px; display: block; color: var(--muted); }
        .field { margin-bottom: 14px; }
        .flash { padding: 14px 16px; border-radius: 12px; margin-bottom: 18px; }
        .flash.success { background: #e6f3eb; color: var(--success); border: 1px solid #b8dfc5; }
        .flash.error { background: #faecea; color: var(--danger); border: 1px solid #e3b9b4; }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 999px; background: #efe4d6; color: #7a5435; font-size: .82rem; }
        .search-bar { display: grid; grid-template-columns: 1fr 180px auto; gap: 12px; align-items: end; margin-bottom: 18px; }
        .pagination { margin-top: 16px; }
        .muted { color: var(--muted); }
        .card-stack { display: grid; gap: 18px; }
        @media (max-width: 980px) { .app,.grid-5,.grid-2,.search-bar { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">Card Bastion</div>
        <div class="subtitle">Servidor central Fase 1</div>
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Productos</a>
        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">Clientes</a>
        <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">Ventas</a>
        <div style="margin-top: 24px;">
            <div class="muted" style="margin-bottom: 10px;">{{ auth()->user()?->name }}</div>
            <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn secondary" type="submit">Cerrar sesión</button></form>
        </div>
    </aside>
    <main class="content">
        <div class="topbar">
            <div>
                <h1 style="margin: 0 0 6px;">{{ $heading ?? 'Panel administrativo' }}</h1>
                <div class="muted">{{ $subheading ?? 'Gestión central de productos, clientes, ventas y sincronización.' }}</div>
            </div>
        </div>
        @if (session('success')) <div class="flash success">{{ session('success') }}</div> @endif
        @if ($errors->any())
            <div class="flash error">
                <strong>Hay datos por corregir.</strong>
                <ul style="margin: 8px 0 0 18px;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
