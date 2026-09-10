@extends('guest-layout')

@section('form-content')
    <div class="auth-form-header">
        <div class="auth-form-logo anim-scale-in">
            <img src="{{ asset('image/logo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <h1 class="anim-fade-up">{{ __('Bienvenido') }}</h1>
        <p class="anim-fade-up stagger-1">{{ __('Ingresa a tu cuenta para continuar.') }}</p>
    </div>

    @if (session('status'))
        <div class="auth-session-status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="auth-session-status" style="color:#ef4444">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
        @csrf

        <div class="field-group">
            {{-- Email --}}
            <div class="auth-field anim-fade-up stagger-2">
                <label for="email" class="auth-field-label">{{ __('Correo electrónico') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="correo@empresa.com"
                    class="auth-field-input @error('email') error @enderror"
                >
                @error('email')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="auth-field anim-fade-up stagger-3">
                <label for="password" class="auth-field-label">{{ __('Contraseña') }}</label>
                <div class="auth-password-wrap" x-data="{ show: false }">
                    <input
                        id="password"
                        :type="show ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                        class="auth-field-input @error('password') error @enderror"
                    >
                    <button type="button" class="auth-password-toggle" @click="show = !show" tabindex="-1">
                        <svg x-show="!show" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 0 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        <svg x-show="show" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="auth-field-row anim-fade-up stagger-4">
            <label class="auth-checkbox">
                <input type="checkbox" name="remember" id="remember_me">
                <span class="auth-checkbox-box">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </span>
                <span class="auth-checkbox-label">{{ __('Recordarme') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <div class="anim-fade-up stagger-5">
            <button type="submit" class="auth-btn">
                <span class="shimmer"></span>
                <span class="text">{{ __('Iniciar sesión') }}</span>
            </button>
        </div>
    </form>
@endsection
