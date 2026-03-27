@extends('layouts.app', ['title' => 'Contenido del sitio', 'heading' => 'Contenido del sitio', 'subheading' => 'Personaliza textos clave, branding y mensajes visibles en la tienda publica.', 'eyebrow' => 'Solo administradores'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="panel">
            <form method="POST" action="{{ route('site-settings.update') }}" class="space-y-8">
                @csrf
                @method('PUT')

                <section class="space-y-5">
                    <div>
                        <p class="section-kicker">Branding</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Identidad visible</h2>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="field">
                            <label for="site_name">Nombre del sitio</label>
                            <input id="site_name" type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}" required>
                        </div>
                        <div class="field">
                            <label for="site_tagline">Subtitulo</label>
                            <input id="site_tagline" type="text" name="site_tagline" value="{{ old('site_tagline', $settings->site_tagline) }}">
                        </div>
                    </div>

                    <div class="field">
                        <label for="announcement_text">Mensaje superior opcional</label>
                        <input id="announcement_text" type="text" name="announcement_text" value="{{ old('announcement_text', $settings->announcement_text) }}" placeholder="Ej. Envios a todo Mexico o evento especial del fin de semana">
                    </div>
                </section>

                <section class="space-y-5">
                    <div>
                        <p class="section-kicker">Home publica</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Hero principal</h2>
                    </div>

                    <div class="field">
                        <label for="home_kicker">Kicker</label>
                        <input id="home_kicker" type="text" name="home_kicker" value="{{ old('home_kicker', $settings->home_kicker) }}">
                    </div>
                    <div class="field">
                        <label for="home_headline">Titular principal</label>
                        <input id="home_headline" type="text" name="home_headline" value="{{ old('home_headline', $settings->home_headline) }}" required>
                    </div>
                    <div class="field">
                        <label for="home_description">Descripcion principal</label>
                        <textarea id="home_description" name="home_description" rows="5" required>{{ old('home_description', $settings->home_description) }}</textarea>
                    </div>
                </section>

                <section class="space-y-5">
                    <div>
                        <p class="section-kicker">Catalogo</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Contexto comercial</h2>
                    </div>

                    <div class="field">
                        <label for="catalog_heading">Encabezado del catalogo</label>
                        <input id="catalog_heading" type="text" name="catalog_heading" value="{{ old('catalog_heading', $settings->catalog_heading) }}" required>
                    </div>
                    <div class="field">
                        <label for="catalog_description">Descripcion del catalogo</label>
                        <textarea id="catalog_description" name="catalog_description" rows="4" required>{{ old('catalog_description', $settings->catalog_description) }}</textarea>
                    </div>
                </section>

                <section class="space-y-5">
                    <div>
                        <p class="section-kicker">Beneficios</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Bloques informativos</h2>
                    </div>

                    <div class="grid gap-5">
                        <div class="rounded-3xl border border-[color:var(--color-line)] p-5">
                            <div class="field">
                                <label for="benefit_one_title">Beneficio 1 titulo</label>
                                <input id="benefit_one_title" type="text" name="benefit_one_title" value="{{ old('benefit_one_title', $settings->benefit_one_title) }}" required>
                            </div>
                            <div class="field mt-4">
                                <label for="benefit_one_description">Beneficio 1 descripcion</label>
                                <textarea id="benefit_one_description" name="benefit_one_description" rows="3" required>{{ old('benefit_one_description', $settings->benefit_one_description) }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-[color:var(--color-line)] p-5">
                            <div class="field">
                                <label for="benefit_two_title">Beneficio 2 titulo</label>
                                <input id="benefit_two_title" type="text" name="benefit_two_title" value="{{ old('benefit_two_title', $settings->benefit_two_title) }}" required>
                            </div>
                            <div class="field mt-4">
                                <label for="benefit_two_description">Beneficio 2 descripcion</label>
                                <textarea id="benefit_two_description" name="benefit_two_description" rows="3" required>{{ old('benefit_two_description', $settings->benefit_two_description) }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-[color:var(--color-line)] p-5">
                            <div class="field">
                                <label for="benefit_three_title">Beneficio 3 titulo</label>
                                <input id="benefit_three_title" type="text" name="benefit_three_title" value="{{ old('benefit_three_title', $settings->benefit_three_title) }}" required>
                            </div>
                            <div class="field mt-4">
                                <label for="benefit_three_description">Beneficio 3 descripcion</label>
                                <textarea id="benefit_three_description" name="benefit_three_description" rows="3" required>{{ old('benefit_three_description', $settings->benefit_three_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Guardar personalizacion</button>
                    <a class="btn btn-secondary" href="{{ route('dashboard') }}">Volver al dashboard</a>
                </div>
            </form>
        </div>

        <div class="panel">
            <p class="section-kicker">Acceso</p>
            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Control exclusivo de administradores</h2>
            <p class="mt-4 text-sm leading-7 text-stone-600">
                Esta seccion esta pensada para gestionar el contenido visible del sitio sin editar codigo. Managers y cashiers podran operar la tienda, pero no cambiar el contenido institucional ni comercial.
            </p>

            <div class="mt-6 space-y-3">
                <div class="rounded-2xl border border-stone-200 px-4 py-4">
                    <p class="font-semibold text-stone-900">Que puedes personalizar aqui</p>
                    <p class="mt-2 text-sm text-stone-500">Nombre del sitio, subtitulo, hero principal, mensajes de catalogo y bloques de beneficios.</p>
                </div>
                <div class="rounded-2xl border border-stone-200 px-4 py-4">
                    <p class="font-semibold text-stone-900">Donde impacta</p>
                    <p class="mt-2 text-sm text-stone-500">Header publico, portada de tienda y mensaje principal de navegacion comercial.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
