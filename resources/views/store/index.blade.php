@extends('layouts.public', ['title' => 'Tienda | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Ecommerce Card Bastion</p>
                <h1 class="mt-4 text-5xl font-black uppercase leading-none tracking-[0.07em] text-stone-900">Una tienda pensada para jugadores.</h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-stone-600">
                    Catalogo publico con productos destacados, categorias y detalle listo para escalar hacia carrito, checkout y portal competitivo.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a class="btn btn-primary" href="#catalogo">Explorar catalogo</a>
                    @guest
                        <a class="btn btn-secondary" href="{{ route('register') }}">Crear cuenta de jugador</a>
                    @endguest
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($featuredProducts as $featured)
                    <a href="{{ route('store.products.show', $featured) }}" class="panel block overflow-hidden">
                        <div class="aspect-[4/3] rounded-2xl bg-stone-100">
                            @if ($featured->primary_image_url)
                                <img class="h-full w-full rounded-2xl object-cover" src="{{ $featured->primary_image_url }}" alt="{{ $featured->name }}">
                            @else
                                <div class="flex h-full items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 to-stone-200 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">{{ $featured->name }}</div>
                            @endif
                        </div>
                        <p class="mt-4 text-xs uppercase tracking-[0.22em] text-stone-500">{{ $featured->categoryModel?->name ?? 'General' }}</p>
                        <h2 class="mt-2 text-lg font-black uppercase tracking-[0.06em] text-stone-900">{{ $featured->name }}</h2>
                        <p class="mt-2 text-sm text-stone-500">${{ number_format($featured->price, 2) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section id="catalogo" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
            <aside class="panel h-fit">
                <form method="GET" class="space-y-5">
                    <div class="field">
                        <label for="search">Buscar</label>
                        <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, SKU o descripcion">
                    </div>
                    <div class="field">
                        <label for="category">Categoria</label>
                        <select id="category" name="category">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }} ({{ $category->products_count }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary w-full" type="submit">Aplicar filtros</button>
                </form>
            </aside>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.24em] text-stone-500">Catalogo</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.08em] text-stone-900">{{ $products->total() }} productos listos para la tienda</h2>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($products as $product)
                        <div class="panel overflow-hidden">
                            <a href="{{ route('store.products.show', $product) }}" class="block">
                            <div class="aspect-[4/3] rounded-2xl bg-stone-100">
                                @if ($product->primary_image_url)
                                    <img class="h-full w-full rounded-2xl object-cover" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}">
                                @else
                                    <div class="flex h-full items-center justify-center rounded-2xl bg-gradient-to-br from-stone-100 to-stone-200 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">{{ $product->name }}</div>
                                @endif
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="text-xs uppercase tracking-[0.22em] text-stone-500">{{ $product->categoryModel?->name ?? 'General' }}</span>
                                @if ($product->featured)
                                    <span class="badge">Featured</span>
                                @endif
                            </div>
                            <h3 class="mt-3 text-lg font-black uppercase tracking-[0.06em] text-stone-900">{{ $product->name }}</h3>
                            <p class="mt-2 text-sm leading-7 text-stone-600">{{ $product->short_description ?: \Illuminate\Support\Str::limit($product->description, 120) }}</p>
                            <div class="mt-5 flex items-center justify-between">
                                <span class="text-xl font-black text-[color:var(--color-brand-600)]">${{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-stone-500">{{ $product->stock }} disponibles</span>
                            </div>
                            </a>
                            <form method="POST" action="{{ route('cart.store') }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button class="btn btn-primary w-full {{ $product->stock <= 0 ? 'pointer-events-none opacity-50' : '' }}" type="submit">
                                    {{ $product->stock <= 0 ? 'Sin stock' : 'Agregar al carrito' }}
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="panel sm:col-span-2 xl:col-span-3">
                            <p class="text-sm text-stone-500">No encontramos productos con esos filtros.</p>
                        </div>
                    @endforelse
                </div>

                <div>{{ $products->links() }}</div>
            </div>
        </div>
    </section>
@endsection
