@extends('layouts.public', ['title' => $product->name.' | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-4">
                <div class="panel overflow-hidden">
                    <div class="aspect-[4/3] rounded-2xl bg-stone-100">
                        @if ($product->primary_image_url)
                            <img class="h-full w-full rounded-2xl object-cover" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="flex h-full items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 to-stone-200 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">{{ $product->name }}</div>
                        @endif
                    </div>
                </div>

                @if ($product->images->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach ($product->images as $image)
                            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white p-2">
                                <img class="aspect-square w-full rounded-xl object-cover" src="{{ $image->url }}" alt="{{ $image->alt_text ?: $product->name }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="panel">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">{{ $product->categoryModel?->name ?? 'General' }}</p>
                <h1 class="mt-3 text-4xl font-black uppercase tracking-[0.06em] text-stone-900">{{ $product->name }}</h1>
                <p class="mt-4 text-base leading-8 text-stone-600">{{ $product->description ?: $product->short_description }}</p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Precio</p>
                        <p class="mt-3 text-2xl font-black text-[color:var(--color-brand-600)]">${{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Stock</p>
                        <p class="mt-3 text-2xl font-black text-stone-900">{{ $product->stock }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.24em] text-stone-500">SKU</p>
                        <p class="mt-3 text-lg font-black text-stone-900">{{ $product->sku }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('cart.store') }}" class="flex flex-wrap gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input class="w-24 rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm" type="number" name="quantity" min="1" max="{{ max($product->stock, 1) }}" value="1" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                        <button class="btn btn-primary {{ $product->stock <= 0 ? 'pointer-events-none opacity-50' : '' }}" type="submit">{{ $product->stock <= 0 ? 'Sin stock' : 'Agregar al carrito' }}</button>
                    </form>
                    <a class="btn btn-secondary" href="{{ route('store.catalog') }}">Volver al catalogo</a>
                </div>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-2xl font-black uppercase tracking-[0.08em] text-stone-900">Relacionados</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                @forelse ($relatedProducts as $related)
                    <a href="{{ route('store.products.show', $related) }}" class="panel block">
                        <p class="text-xs uppercase tracking-[0.22em] text-stone-500">{{ $related->categoryModel?->name ?? 'General' }}</p>
                        <h3 class="mt-2 text-lg font-black uppercase tracking-[0.06em] text-stone-900">{{ $related->name }}</h3>
                        <p class="mt-2 text-sm text-stone-500">${{ number_format($related->price, 2) }}</p>
                    </a>
                @empty
                    <div class="panel sm:col-span-2 xl:col-span-4">
                        <p class="text-sm text-stone-500">Todavia no hay productos relacionados visibles.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
