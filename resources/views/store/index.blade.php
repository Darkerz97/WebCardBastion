@extends('layouts.public', ['title' => 'Tienda | Card Bastion'])

@php
    $hasFilters = filled(request('search')) || filled(request('category'));
    $activeCategory = $categories->firstWhere('slug', $selectedCategory);
    $visibleProducts = $products->count();
@endphp

@section('content')
    <section class="mx-auto max-w-7xl px-4 pb-8 pt-8 sm:px-6 lg:px-8 lg:pb-10 lg:pt-10">
        <div class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr] lg:items-stretch">
            <div class="panel relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top_left,rgba(238,216,191,0.55),transparent_60%)]"></div>
                <div class="relative">
                    <p class="section-kicker eyebrow-dot">Card Bastion Store</p>
                    <h1 class="mt-5 max-w-3xl text-4xl font-black uppercase leading-[0.95] tracking-[0.05em] text-stone-900 sm:text-5xl xl:text-6xl">
                        Cartas, accesorios y picks curados para jugadores que sí cuidan su mesa.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-[color:var(--color-ink-soft)] sm:text-lg">
                        Descubre un catálogo especializado de TCG con selección premium, novedades en rotación y una experiencia de compra pensada para jugadores competitivos y coleccionistas.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a class="btn btn-primary" href="#catalogo">Explorar catálogo</a>
                        <a class="btn btn-secondary" href="{{ route('cart.index') }}">Ver carrito</a>
                        @guest
                            <a class="btn btn-secondary" href="{{ route('register') }}">Unirme a la comunidad</a>
                        @endguest
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-[color:var(--color-line)] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-stone-500">Selección</p>
                            <p class="mt-2 text-2xl font-black text-stone-900">{{ $products->total() }}</p>
                            <p class="mt-1 text-sm text-[color:var(--color-ink-soft)]">productos publicados</p>
                        </div>
                        <div class="rounded-2xl border border-[color:var(--color-line)] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-stone-500">Categorías</p>
                            <p class="mt-2 text-2xl font-black text-stone-900">{{ $categories->count() }}</p>
                            <p class="mt-1 text-sm text-[color:var(--color-ink-soft)]">líneas activas en tienda</p>
                        </div>
                        <div class="rounded-2xl border border-[color:var(--color-line)] bg-white/80 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-stone-500">Envío</p>
                            <p class="mt-2 text-2xl font-black text-stone-900">Simple</p>
                            <p class="mt-1 text-sm text-[color:var(--color-ink-soft)]">compra clara y rápida</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @forelse ($featuredProducts as $index => $featured)
                    <a href="{{ route('store.products.show', $featured) }}" class="catalog-card {{ $index === 0 ? 'sm:col-span-2' : '' }}">
                        <div class="aspect-[4/3] overflow-hidden rounded-[22px] bg-stone-100 {{ $index === 0 ? 'sm:aspect-[2.4/1]' : '' }}">
                            @if ($featured->primary_image_url)
                                <img class="h-full w-full object-cover transition duration-300 hover:scale-[1.03]" src="{{ $featured->primary_image_url }}" alt="{{ $featured->name }}">
                            @else
                                <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,rgba(238,216,191,0.85),rgba(246,240,232,1))] px-6 text-center text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">
                                    {{ $featured->name }}
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">{{ $featured->categoryModel?->name ?? 'Selección destacada' }}</p>
                                <h2 class="mt-2 text-lg font-black uppercase tracking-[0.05em] text-stone-900">{{ $featured->name }}</h2>
                            </div>
                            <span class="badge">Destacado</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <p class="text-sm text-[color:var(--color-ink-soft)]">{{ $featured->short_description ?: \Illuminate\Support\Str::limit($featured->description, 68) }}</p>
                            <span class="shrink-0 text-lg font-black text-[color:var(--color-brand-600)]">${{ number_format($featured->price, 2) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="panel-muted sm:col-span-2">
                        <p class="section-kicker">Selección destacada</p>
                        <h2 class="mt-3 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Estamos preparando vitrinas premium para la tienda.</h2>
                        <p class="mt-4 text-sm leading-7 text-[color:var(--color-ink-soft)]">
                            Muy pronto verás aquí lanzamientos, staples y picks curados por categoría para que encontrar tu próxima compra sea más rápido.
                        </p>
                        <a class="btn btn-secondary mt-6" href="#catalogo">Ir al catálogo</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="panel-muted">
                <p class="section-kicker">Catálogo curado</p>
                <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">Selección enfocada en juego real</h2>
                <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">Productos organizados para que ubiques staples, accesorios y cartas útiles sin navegar una tienda caótica.</p>
            </div>
            <div class="panel-muted">
                <p class="section-kicker">Comunidad y eventos</p>
                <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">Tienda conectada con jugadores</h2>
                <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">El ecosistema de Card Bastion está pensado para combinar compra, torneos y seguimiento de cuenta en una sola experiencia.</p>
            </div>
            <div class="panel-muted">
                <p class="section-kicker">Compra simple</p>
                <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">Proceso claro desde el primer clic</h2>
                <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">Filtros directos, fichas limpias y carrito visible para que la navegación se sienta rápida, elegante y confiable.</p>
            </div>
        </div>
    </section>

    <section id="catalogo" class="mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 rounded-[32px] border border-[color:var(--color-line)] bg-[linear-gradient(135deg,rgba(255,253,249,0.95),rgba(246,240,232,0.9))] px-6 py-6 shadow-[0_20px_50px_rgba(36,24,18,0.07)] lg:flex-row lg:items-end lg:justify-between lg:px-8">
            <div class="max-w-2xl">
                <p class="section-kicker">Catálogo</p>
                <h2 class="mt-3 section-title">Explora la tienda con mejor contexto y menos ruido.</h2>
                <p class="mt-4 text-sm leading-7 text-[color:var(--color-ink-soft)] sm:text-base">
                    Filtra por categoría, busca cartas o productos clave y revisa una selección presentada con mejor jerarquía visual para compra rápida.
                </p>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-[color:var(--color-line)] bg-white/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">Resultados</p>
                    <p class="mt-2 text-2xl font-black text-stone-900">{{ $products->total() }}</p>
                </div>
                <div class="rounded-2xl border border-[color:var(--color-line)] bg-white/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">Vista actual</p>
                    <p class="mt-2 text-2xl font-black text-stone-900">{{ $visibleProducts }}</p>
                </div>
                <div class="rounded-2xl border border-[color:var(--color-line)] bg-white/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">Filtro activo</p>
                    <p class="mt-2 truncate text-base font-black uppercase tracking-[0.05em] text-stone-900">{{ $activeCategory?->name ?? 'Todos' }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <aside class="panel h-fit">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="section-kicker">Refina tu búsqueda</p>
                        <h3 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Filtros</h3>
                    </div>
                    @if ($hasFilters)
                        <a class="text-sm font-semibold text-[color:var(--color-brand-600)] transition hover:text-[color:var(--color-brand-700)]" href="{{ route('store.catalog') }}">Limpiar</a>
                    @endif
                </div>

                <form method="GET" class="mt-6 space-y-5">
                    <div class="field">
                        <label for="search">Buscar producto</label>
                        <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, SKU o descripción">
                    </div>
                    <div class="field">
                        <label for="category">Categoría</label>
                        <select id="category" name="category">
                            <option value="">Todas las categorías</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }} ({{ $category->products_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rounded-2xl border border-[color:var(--color-line)] bg-[color:var(--color-brand-50)] px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--color-brand-600)]">Consejo de navegación</p>
                        <p class="mt-2 text-sm leading-6 text-[color:var(--color-ink-soft)]">Usa el buscador para staples, nombres de sets o referencias rápidas y cambia de categoría para descubrir líneas completas.</p>
                    </div>

                    <button class="btn btn-primary w-full" type="submit">Aplicar filtros</button>
                </form>
            </aside>

            <div class="space-y-6">
                @if ($products->total() > 0)
                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <div class="catalog-card overflow-hidden">
                                <a href="{{ route('store.products.show', $product) }}" class="block">
                                    <div class="aspect-[4/3] overflow-hidden rounded-[22px] bg-stone-100">
                                        @if ($product->primary_image_url)
                                            <img class="h-full w-full object-cover transition duration-300 hover:scale-[1.03]" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,rgba(238,216,191,0.85),rgba(246,240,232,1))] px-6 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">
                                                {{ $product->name }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex items-center justify-between gap-3">
                                        <span class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">{{ $product->categoryModel?->name ?? 'General' }}</span>
                                        @if ($product->featured)
                                            <span class="badge">Destacado</span>
                                        @endif
                                    </div>

                                    <h3 class="mt-3 text-xl font-black uppercase tracking-[0.04em] text-stone-900">{{ $product->name }}</h3>
                                    <p class="mt-3 min-h-14 text-sm leading-7 text-[color:var(--color-ink-soft)]">{{ $product->short_description ?: \Illuminate\Support\Str::limit($product->description, 120) }}</p>

                                    <div class="mt-5 flex items-end justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">Precio</p>
                                            <span class="mt-1 block text-2xl font-black text-[color:var(--color-brand-600)]">${{ number_format($product->price, 2) }}</span>
                                        </div>
                                        <span class="rounded-full border border-[color:var(--color-line)] bg-white px-3 py-1 text-sm text-stone-600">{{ $product->stock }} disponibles</span>
                                    </div>
                                </a>

                                <form method="POST" action="{{ route('cart.store') }}" class="mt-5">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn btn-primary w-full {{ $product->stock <= 0 ? 'pointer-events-none opacity-50' : '' }}" type="submit">
                                        {{ $product->stock <= 0 ? 'Sin stock' : 'Agregar al carrito' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="panel-muted">
                        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                            <div>
                                <p class="section-kicker">{{ $hasFilters ? 'Sin coincidencias' : 'Catálogo en preparación' }}</p>
                                <h3 class="mt-3 text-3xl font-black uppercase leading-tight tracking-[0.05em] text-stone-900">
                                    {{ $hasFilters ? 'No encontramos productos con esta búsqueda, pero la tienda sigue activa.' : 'Estamos afinando la siguiente vitrina de productos para Card Bastion.' }}
                                </h3>
                                <p class="mt-4 max-w-2xl text-sm leading-7 text-[color:var(--color-ink-soft)] sm:text-base">
                                    {{ $hasFilters
                                        ? 'Prueba ajustando el texto o cambiando de categoría para descubrir nuevas coincidencias. El catálogo puede variar según disponibilidad y rotación.'
                                        : 'La selección pública se está curando para mostrar artículos con mejor disponibilidad y presentación. Vuelve pronto o explora otras secciones de la experiencia Card Bastion.' }}
                                </p>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    @if ($hasFilters)
                                        <a class="btn btn-primary" href="{{ route('store.catalog') }}">Limpiar filtros</a>
                                    @endif
                                    <a class="btn btn-secondary" href="{{ route('store.home') }}">Volver al inicio</a>
                                </div>
                            </div>

                            <div class="grid gap-3">
                                <div class="rounded-2xl border border-[color:var(--color-line)] bg-white px-5 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">Siguiente paso</p>
                                    <p class="mt-2 text-sm leading-6 text-[color:var(--color-ink-soft)]">{{ $hasFilters ? 'Explora todas las categorías o intenta una búsqueda más amplia.' : 'Esta sección ya quedó preparada para mostrar productos reales y destacados cuando se publiquen.' }}</p>
                                </div>
                                <div class="rounded-2xl border border-[color:var(--color-line)] bg-white px-5 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">Experiencia lista</p>
                                    <p class="mt-2 text-sm leading-6 text-[color:var(--color-ink-soft)]">El diseño del catálogo ya está preparado para tarjetas, imágenes, filtros y navegación comercial completa.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div>{{ $products->links() }}</div>
            </div>
        </div>
    </section>
@endsection
