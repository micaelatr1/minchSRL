<nav class="navbar">
    <div class="container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <div class="navbar-logo">
                <img src="{{ asset('image/logo.png') }}" alt="MINCH SRL">
            </div>
            <div class="navbar-brand-text">
                <span class="name">MINCH SRL</span>
                <span class="sub">{{ __('Compra, venta y procesamiento de mineral') }}</span>
            </div>
        </a>
        <div class="navbar-actions">
            @auth
                <a href="{{ route('profile.edit') }}" class="nav-btn nav-btn-primary nav-btn-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                    {{ __('Dashboard') }}
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">{{ __('Log in') }}</a>
                @endif

            @endauth
        </div>
    </div>
</nav>
