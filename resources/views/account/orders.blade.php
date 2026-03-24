@extends('layouts.public', ['title' => 'Mis compras | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Historial</p>
                <h1 class="mt-3 text-4xl font-black uppercase tracking-[0.06em] text-stone-900">Mis compras</h1>
            </div>
            <a class="btn btn-secondary" href="{{ route('account.dashboard') }}">Volver a mi cuenta</a>
        </div>

        <div class="mt-8 space-y-4">
            @forelse ($orders as $order)
                <article class="panel">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">{{ $order->sale_number }}</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.06em] text-stone-900">${{ number_format($order->total, 2) }}</h2>
                            <p class="mt-2 text-sm text-stone-500">{{ optional($order->sold_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="badge">{{ $order->status }}</span>
                            <span class="badge">{{ $order->payment_status }}</span>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between rounded-2xl border border-stone-200 px-4 py-3 text-sm">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $item->product?->name ?? 'Producto eliminado' }}</p>
                                    <p class="text-stone-500">Cantidad: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-stone-900">${{ number_format($item->line_total, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="panel">
                    <p class="text-sm text-stone-500">Aun no tienes pedidos registrados en la tienda.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </section>
@endsection
