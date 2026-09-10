@extends('layouts.public')

@section('content')
    <div class="auth-wrap">
        {{-- Brand Panel --}}
        <aside class="auth-brand">
            <div class="auth-brand-grid"></div>
            <div class="auth-orb auth-orb--1 anim-float-1"></div>
            <div class="auth-orb auth-orb--2 anim-float-2"></div>
            <div class="auth-orb auth-orb--3 anim-float-3"></div>
            <div class="auth-mesh"></div>
            <div class="anim-shimmer" style="position:absolute;inset:0;pointer-events:none"></div>
            <div class="auth-accent-line"></div>

            <div class="auth-brand-top anim-fade-in">
                <div class="auth-logo">
                    <div class="auth-logo-icon">
                        <img src="{{ asset('image/logo.png') }}" alt="{{ config('app.name') }}">
                    </div>
                    <div class="auth-logo-text">
                        <span class="name">MINCH SRL</span>
                        <span class="sub">{{ __('Compra, venta y procesamiento de minerales') }}</span>
                    </div>
                </div>
            </div>

            <div class="auth-brand-center">
                <h2 class="auth-title anim-fade-up stagger-1">
                    {{ __('Minerales con') }}<br>
                    <span class="highlight">{{ __('responsabilidad') }}</span>
                </h2>

                <p class="auth-desc anim-fade-up stagger-2">
                    {{ __('Sistema integral para la administración de MINCH SRL') }}<br>
                    <strong>{{ __('compra, venta, procesamiento y logística minera.') }}</strong>
                </p>

                <div class="auth-benefits">
                    @php
                        $items = [
                            [__('Compra responsable de minerales'), 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 0 4.5 6h.75m13.5 0h.75a.75.75 0 0 0 .75-.75V4.5M4.5 18.75h15m-15 0V8.25a2.25 2.25 0 0 1 2.25-2.25h10.5A2.25 2.25 0 0 1 19.5 8.25v10.5'],
                            [__('Procesamiento con calidad certificada'), 'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605'],
                            [__('Logística y distribución eficiente'), 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z'],
                            [__('Gestión administrativa integrada'), 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                        ];
                    @endphp
                    @foreach ($items as $i => $item)
                        <div class="auth-benefit anim-fade-up" style="animation-delay: {{ 0.3 + $i * 0.08 }}s">
                            <span class="auth-benefit-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item[1] }}" />
                                </svg>
                            </span>
                            {{ $item[0] }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="auth-brand-bottom anim-fade-in">
                <div class="auth-brand-footer">
                    <span class="copy">&copy; {{ date('Y') }} MINCH SRL.</span>
                    <span class="tagline">&#9670; Comprometidos con la minería responsable &#9670;</span>
                </div>
            </div>
        </aside>

        {{-- Form Panel --}}
        <main class="auth-form-panel">
            <div class="auth-form-grid"></div>
            <div class="auth-form-glow"></div>
            <div class="auth-form-wrap">
                <div class="auth-form-card">
                    @yield('form-content')
                </div>
            </div>
        </main>
    </div>
@endsection
