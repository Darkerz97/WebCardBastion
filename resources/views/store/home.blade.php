@extends('layouts.public', ['title' => ($siteSettings?->site_name ?? 'Card Bastion').' | Inicio'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 pb-8 pt-8 sm:px-6 lg:px-8 lg:pb-10 lg:pt-10">
        <div class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr] lg:items-stretch">
            <div class="panel relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top_left,rgba(238,216,191,0.55),transparent_60%)]"></div>
                <div class="relative">
                    <p class="section-kicker eyebrow-dot">{{ $siteSettings?->home_kicker ?? 'Card Bastion Store' }}</p>
                    <h1 class="mt-5 max-w-3xl text-4xl font-black uppercase leading-[0.95] tracking-[0.05em] text-stone-900 sm:text-5xl xl:text-6xl">
                        {{ $siteSettings?->home_headline ?? 'Cartas, accesorios y picks curados para jugadores que si cuidan su mesa.' }}
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-[color:var(--color-ink-soft)] sm:text-lg">
                        {{ $siteSettings?->home_description ?? 'Descubre un catalogo especializado de TCG con seleccion premium, novedades en rotacion y una experiencia de compra pensada para jugadores competitivos y coleccionistas.' }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a class="btn btn-primary" href="{{ route('store.catalog') }}">Entrar a la tienda</a>
                        <a class="btn btn-secondary" href="{{ route('blog.index') }}">Ver articulos</a>
                        @guest
                            <a class="btn btn-secondary" href="{{ route('register') }}">Unirme a la comunidad</a>
                        @endguest
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-[rgba(255,171,18,0.28)] bg-[linear-gradient(180deg,rgba(70,45,10,0.95),rgba(43,29,8,0.94))] px-4 py-4 shadow-[0_16px_30px_rgba(0,0,0,0.2)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[rgba(255,214,153,0.72)]">Tienda</p>
                            <p class="mt-2 text-2xl font-black text-[color:var(--color-ink)]">Activa</p>
                            <p class="mt-1 text-sm text-[rgba(255,239,214,0.8)]">catalogo y checkout en linea</p>
                        </div>
                        <div class="rounded-2xl border border-[rgba(99,179,237,0.2)] bg-[linear-gradient(180deg,rgba(18,39,63,0.96),rgba(14,27,44,0.94))] px-4 py-4 shadow-[0_16px_30px_rgba(0,0,0,0.2)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[rgba(173,216,255,0.72)]">Comunidad</p>
                            <p class="mt-2 text-2xl font-black text-[color:var(--color-ink)]">Social</p>
                            <p class="mt-1 text-sm text-[rgba(215,233,255,0.78)]">articulos, redes y novedades</p>
                        </div>
                        <div class="rounded-2xl border border-[rgba(196,156,90,0.2)] bg-[linear-gradient(180deg,rgba(46,36,28,0.96),rgba(31,26,22,0.94))] px-4 py-4 shadow-[0_16px_30px_rgba(0,0,0,0.2)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[rgba(232,205,160,0.72)]">Jugador</p>
                            <p class="mt-2 text-2xl font-black text-[color:var(--color-ink)]">Portal</p>
                            <p class="mt-1 text-sm text-[rgba(235,223,205,0.78)]">cuenta, compras y torneos</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <a href="{{ route('store.catalog') }}" class="catalog-card sm:col-span-2">
                    <div class="rounded-[22px] border border-[rgba(255,171,18,0.18)] bg-[linear-gradient(135deg,rgba(38,29,18,0.96),rgba(18,18,18,0.95))] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[rgba(255,214,153,0.72)]">Explora la tienda</p>
                        <h2 class="mt-3 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Separa la experiencia de compra y entra directo al catalogo.</h2>
                        <p class="mt-4 text-sm leading-7 text-[color:var(--color-ink-soft)]">Ahora la portada principal se enfoca en marca, comunidad y contenido. La tienda vive en una vista dedicada para navegar productos con mas claridad.</p>
                    </div>
                </a>
                <a href="{{ route('blog.index') }}" class="catalog-card">
                    <p class="section-kicker">Editorial</p>
                    <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">Articulos y vlog</h2>
                    <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">Noticias, recapitulaciones, comunidad y actualizaciones de Card Bastion.</p>
                </a>
                <a href="{{ route('account.tournaments.index') }}" class="catalog-card">
                    <p class="section-kicker">Jugadores</p>
                    <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">Portal y torneos</h2>
                    <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">Consulta tu cuenta, historial y actividad competitiva desde un solo lugar.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="panel-muted">
                <p class="section-kicker">Catalogo curado</p>
                <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">{{ $siteSettings?->benefit_one_title ?? 'Seleccion enfocada en juego real' }}</h2>
                <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">{{ $siteSettings?->benefit_one_description ?? 'Productos organizados para que ubiques staples, accesorios y cartas utiles sin navegar una tienda caotica.' }}</p>
            </div>
            <div class="panel-muted">
                <p class="section-kicker">Comunidad y eventos</p>
                <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">{{ $siteSettings?->benefit_two_title ?? 'Tienda conectada con jugadores' }}</h2>
                <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">{{ $siteSettings?->benefit_two_description ?? 'El ecosistema de Card Bastion esta pensado para combinar compra, torneos y seguimiento de cuenta en una sola experiencia.' }}</p>
            </div>
            <div class="panel-muted">
                <p class="section-kicker">Compra simple</p>
                <h2 class="mt-3 text-xl font-black uppercase tracking-[0.05em] text-stone-900">{{ $siteSettings?->benefit_three_title ?? 'Proceso claro desde el primer clic' }}</h2>
                <p class="mt-3 text-sm leading-7 text-[color:var(--color-ink-soft)]">{{ $siteSettings?->benefit_three_description ?? 'Filtros directos, fichas limpias y carrito visible para que la navegacion se sienta rapida, elegante y confiable.' }}</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8">
        <div class="panel overflow-hidden">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="section-kicker">Comunidad social</p>
                    <h2 class="mt-3 section-title">{{ $siteSettings?->social_heading ?? 'Sigue la comunidad Card Bastion en tiempo real.' }}</h2>
                    <p class="mt-4 text-sm leading-7 text-[color:var(--color-ink-soft)] sm:text-base">
                        {{ $siteSettings?->social_description ?? 'Conecta Facebook, Instagram y TikTok para mostrar albumes, publicaciones recientes y accesos directos a tus perfiles oficiales.' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if (filled($siteSettings?->facebook_url))
                        <a class="btn btn-secondary" href="{{ $siteSettings->facebook_url }}" target="_blank" rel="noreferrer">Seguir en Facebook</a>
                    @endif
                    @if (filled($siteSettings?->instagram_url))
                        <a class="btn btn-secondary" href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noreferrer">Seguir en Instagram</a>
                    @endif
                    @if (filled($siteSettings?->tiktok_url))
                        <a class="btn btn-secondary" href="{{ $siteSettings->tiktok_url }}" target="_blank" rel="noreferrer">Seguir en TikTok</a>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid gap-5 xl:grid-cols-3">
                <div class="rounded-[28px] border border-[rgba(99,126,255,0.16)] bg-[linear-gradient(180deg,rgba(19,28,47,0.98),rgba(12,20,35,0.96))] p-5 shadow-[0_18px_36px_rgba(0,0,0,0.24)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[rgba(173,216,255,0.72)]">Facebook</p>
                            <h3 class="mt-2 text-xl font-black uppercase tracking-[0.04em] text-[color:var(--color-ink)]">Album y publicaciones</h3>
                        </div>
                        @if (filled($siteSettings?->facebook_url))
                            <a class="badge" href="{{ $siteSettings->facebook_url }}" target="_blank" rel="noreferrer">Abrir</a>
                        @endif
                    </div>
                    <div class="social-embed-shell mt-5 rounded-3xl border border-white/8 bg-[rgba(255,255,255,0.03)] p-3 text-sm text-[color:var(--color-ink-soft)]">
                        @if (filled($siteSettings?->facebook_embed))
                            <div class="social-embed social-embed-facebook">
                                {!! $siteSettings->facebook_embed !!}
                            </div>
                        @else
                            <div class="flex min-h-56 items-center justify-center rounded-2xl border border-dashed border-white/10 px-6 text-center">
                                Agrega desde admin el embed de Facebook para mostrar el album o las ultimas publicaciones.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-[28px] border border-[rgba(255,171,18,0.18)] bg-[linear-gradient(180deg,rgba(46,31,16,0.98),rgba(29,22,16,0.96))] p-5 shadow-[0_18px_36px_rgba(0,0,0,0.24)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[rgba(255,214,153,0.72)]">Instagram</p>
                            <h3 class="mt-2 text-xl font-black uppercase tracking-[0.04em] text-[color:var(--color-ink)]">Feed reciente</h3>
                        </div>
                        @if (filled($siteSettings?->instagram_url))
                            <a class="badge" href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noreferrer">Abrir</a>
                        @endif
                    </div>
                    <div class="social-embed-shell mt-5 rounded-3xl border border-white/8 bg-[rgba(255,255,255,0.03)] p-3 text-sm text-[color:var(--color-ink-soft)]">
                        @if (filled($siteSettings?->instagram_embed))
                            <div class="social-embed social-embed-instagram">
                                {!! $siteSettings->instagram_embed !!}
                            </div>
                        @else
                            <div class="flex min-h-56 items-center justify-center rounded-2xl border border-dashed border-white/10 px-6 text-center">
                                Agrega desde admin el embed de Instagram para mostrar las ultimas publicaciones o un reel destacado.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-[28px] border border-[rgba(255,85,130,0.18)] bg-[linear-gradient(180deg,rgba(37,18,30,0.98),rgba(24,16,22,0.96))] p-5 shadow-[0_18px_36px_rgba(0,0,0,0.24)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[rgba(255,184,207,0.74)]">TikTok</p>
                            <h3 class="mt-2 text-xl font-black uppercase tracking-[0.04em] text-[color:var(--color-ink)]">Videos recientes</h3>
                        </div>
                        @if (filled($siteSettings?->tiktok_url))
                            <a class="badge" href="{{ $siteSettings->tiktok_url }}" target="_blank" rel="noreferrer">Abrir</a>
                        @endif
                    </div>
                    <div class="social-embed-shell mt-5 rounded-3xl border border-white/8 bg-[rgba(255,255,255,0.03)] p-3 text-sm text-[color:var(--color-ink-soft)]">
                        @if (filled($siteSettings?->tiktok_embed))
                            <div class="social-embed social-embed-tiktok">
                                {!! $siteSettings->tiktok_embed !!}
                            </div>
                        @else
                            <div class="flex min-h-56 items-center justify-center rounded-2xl border border-dashed border-white/10 px-6 text-center">
                                Agrega desde admin el embed de TikTok para mostrar clips recientes y atraer visitas al perfil.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
