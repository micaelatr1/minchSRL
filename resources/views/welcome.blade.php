@extends('layouts.public')

@section('content')

    {{-- ═══ HERO ═══ --}}
    <section class="hero">
        <div class="hero-bg-grid"></div>
        <div class="hero-orbs">
            <div class="hero-orb hero-orb--1"></div>
            <div class="hero-orb hero-orb--2"></div>
            <div class="hero-orb hero-orb--3"></div>
        </div>
        <div class="hero-mesh"></div>
        <div class="hero-shimmer"></div>

        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    {{ __('Empresa MINCH SRL') }}<br>
                    <span class="highlight">{{ __('Compra, venta y procesamiento de mineral') }}</span>
                </h1>

                <p class="hero-desc">
                    {{ __('Somos una empresa boliviana dedicada a la compra, venta y procesamiento de mineral. Fundada en febrero de 2025, ofrecemos servicios integrales para el tratamiento de minerales, garantizando calidad, transparencia y cumplimiento normativo en cada operación.') }}
                </p>

                <div class="hero-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            {{ __('Ir al Dashboard') }}
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            {{ __('Iniciar sesión') }}
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    @endauth
                </div>

                <div class="hero-stats">
                    <div>
                        <p class="hero-stat-value">{{ __('2025') }}</p>
                        <p class="hero-stat-label">{{ __('Año de fundación') }}</p>
                    </div>
                    <div>
                        <p class="hero-stat-value">100%</p>
                        <p class="hero-stat-label">{{ __('Compromiso de calidad') }}</p>
                    </div>
                    <div>
                        <p class="hero-stat-value">+50</p>
                        <p class="hero-stat-label">{{ __('Aliados comerciales') }}</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-glow"></div>
                    <div class="hero-card-body" style="padding: 0; overflow: hidden;">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 2rem;">
                            <div style="width: 280px; height: 280px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset('image/logo.png') }}" alt="MINCH SRL" style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
                            <div style="margin-top: 1.5rem; text-align: center;">
                                <h3 style="font-size: 1.5rem; font-weight: 700; color: #fff;">MINCH SRL</h3>
                                <p style="font-size: 0.9rem; color: rgb(148 163 184); margin-top: 0.25rem;">{{ __('Compra, venta y procesamiento de mineral') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>{{ __('Scroll') }}</span>
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3"/></svg>
        </div>
    </section>

    {{-- ═══ ANTECEDENTES ═══ --}}
    <section class="features" style="padding-top: 6rem;">
        <div class="container">
            <div class="features-header">
                <h2 class="features-title">{{ __('Nuestra historia') }}</h2>
            </div>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p style="color: rgb(156 163 175); line-height: 1.8; font-size: 1.05rem;">
                    {{ __('MINCH SRL. nace en febrero de 2025 como una empresa boliviana dedicada a la comercialización y procesamiento de mineral. Desde nuestros inicios, nos hemos enfocado en establecer relaciones comerciales sólidas y transparentes con cooperativas mineras, proveedores y compradores, tanto a nivel nacional como internacional.') }}
                </p>
                <br>
                <p style="color: rgb(156 163 175); line-height: 1.8; font-size: 1.05rem;">
                    {{ __('Nuestra actividad principal abarca la compra responsable de mineral en bruto, su procesamiento y transformación, y la venta de mineral procesado cumpliendo con los más altos estándares de calidad y normativa vigente. Contamos con un equipo comprometido que garantiza trazabilidad, transparencia y valor agregado en cada etapa de la cadena productiva.') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ═══ MISIÓN / VISIÓN ═══ --}}
    <section class="features" style="padding-top: 2rem;">
        <div class="container">
            <div class="features-header" style="text-align: center;">
                <h2 class="features-title">{{ __('Misión y Visión') }}</h2>
            </div>
            <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">
                <article class="feature-card" style="text-align: center;">
                    <div class="feature-icon feature-icon--blue" style="margin: 0 auto 1.5rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width: 2rem; height: 2rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                        </svg>
                    </div>
                    <h3>{{ __('Misión') }}</h3>
                    <p style="color: rgb(156 163 175); line-height: 1.7;">
                        {{ __('Ser una empresa líder en la compra, venta y procesamiento de mineral en Bolivia, ofreciendo un servicio integral que agrega valor a cada etapa de la cadena productiva, desde la adquisición responsable hasta la comercialización final, garantizando transparencia, calidad y cumplimiento normativo.') }}
                    </p>
                </article>

                <article class="feature-card" style="text-align: center;">
                    <div class="feature-icon feature-icon--purple" style="margin: 0 auto 1.5rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width: 2rem; height: 2rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>
                        </svg>
                    </div>
                    <h3>{{ __('Visión') }}</h3>
                    <p style="color: rgb(156 163 175); line-height: 1.7;">
                        {{ __('Ser referentes en el sector minero boliviano, reconocidos por nuestra excelencia operativa, responsabilidad social y capacidad de crecimiento sostenible, contribuyendo al desarrollo económico de las comunidades mineras con las que trabajamos.') }}
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- ═══ VALORES ═══ --}}
    <section class="features" style="padding-top: 2rem;">
        <div class="container">
            <div class="features-header">
                <h2 class="features-title">{{ __('Principios que nos guían') }}</h2>
                <p class="features-sub">{{ __('Nuestra cultura organizacional se fundamenta en valores sólidos que orientan cada operación y relación comercial.') }}</p>
            </div>

            <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">
                <article class="feature-card" style="text-align: center;">
                    <div class="feature-icon feature-icon--blue" style="margin: 0 auto 1.5rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width: 1.75rem; height: 1.75rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                        </svg>
                    </div>
                    <h3>{{ __('Transparencia') }}</h3>
                    <p style="color: rgb(156 163 175); line-height: 1.7;">{{ __('Actuamos con honestidad y claridad en cada operación, garantizando trazabilidad y confianza a nuestros aliados.') }}</p>
                </article>

                <article class="feature-card" style="text-align: center;">
                    <div class="feature-icon feature-icon--green" style="margin: 0 auto 1.5rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width: 1.75rem; height: 1.75rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>
                        </svg>
                    </div>
                    <h3>{{ __('Responsabilidad') }}</h3>
                    <p style="color: rgb(156 163 175); line-height: 1.7;">{{ __('Trabajamos con compromiso social y ambiental, promoviendo prácticas sostenibles en la industria minera.') }}</p>
                </article>

                <article class="feature-card" style="text-align: center;">
                    <div class="feature-icon feature-icon--amber" style="margin: 0 auto 1.5rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width: 1.75rem; height: 1.75rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                        </svg>
                    </div>
                    <h3>{{ __('Calidad') }}</h3>
                    <p style="color: rgb(156 163 175); line-height: 1.7;">{{ __('Nos esforzamos por ofrecer un servicio excelente en cada etapa, desde la recepción del mineral hasta su comercialización final.') }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- ═══ CTA ═══ --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>{{ __('¿Listo para trabajar con nosotros?') }}</h2>
                <p>{{ __('Si eres parte del sector minero y buscas un aliado comercial confiable para la compra, venta o procesamiento de mineral, contáctanos. En MINCH SRL. estamos listos para atenderte.') }}</p>
                <div class="cta-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Ir al Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Iniciar sesión') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

@endsection
