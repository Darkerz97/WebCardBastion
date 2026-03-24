@extends('layouts.public', ['title' => 'Mi cuenta | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="panel">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Portal del jugador</p>
                <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">{{ $user->name }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-stone-600">
                    Este panel ya queda listo como base para historial de compras, torneos, estadisticas y creditos en las siguientes fases.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Compras</p>
                        <p class="mt-3 text-3xl font-black text-stone-900">{{ $stats['orders'] }}</p>
                    </article>
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Creditos</p>
                        <p class="mt-3 text-3xl font-black text-stone-900">${{ number_format($stats['credits'], 2) }}</p>
                    </article>
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Torneos</p>
                        <p class="mt-3 text-3xl font-black text-stone-900">{{ $stats['tournaments'] }}</p>
                    </article>
                    <article class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Win rate</p>
                        <p class="mt-3 text-2xl font-black text-stone-900">{{ $stats['win_rate'] }}</p>
                    </article>
                </div>
            </div>

            <div class="panel">
                <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Perfil actual</h2>
                <dl class="mt-6 space-y-4 text-sm">
                    <div>
                        <dt class="font-semibold text-stone-500">Correo</dt>
                        <dd class="mt-1 text-stone-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-stone-500">Telefono</dt>
                        <dd class="mt-1 text-stone-900">{{ $user->phone ?: 'Sin registrar' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-stone-500">Rol</dt>
                        <dd class="mt-1 text-stone-900">{{ $user->role?->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-stone-500">Perfil comercial vinculado</dt>
                        <dd class="mt-1 text-stone-900">{{ $customerProfile?->name ?: 'Aun no vinculado a cliente' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="panel mt-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Compras recientes</h2>
                    <p class="mt-2 text-sm text-stone-500">Resumen rapido de tus pedidos en la tienda virtual.</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('account.orders.index') }}">Ver historial completo</a>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($recentOrders as $order)
                    <div class="rounded-2xl border border-stone-200 px-4 py-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $order->sale_number }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ optional($order->sold_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-sm text-stone-600">
                                <span class="badge">{{ $order->payment_status }}</span>
                                <span class="ml-3 font-semibold text-stone-900">${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-stone-500">Aun no tienes compras registradas en la tienda.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
