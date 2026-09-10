@extends('guest-layout')

@section('form-content')
    <div class="auth-form-header">
        <div class="auth-form-logo anim-scale-in">
            <img src="{{ asset('image/logo.png') }}" alt="MINCH SRL">
        </div>
        <h1 class="anim-fade-up">{{ __('¿Olvidaste tu contraseña?') }}</h1>
        <p class="anim-fade-up stagger-1">{{ __('Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.') }}</p>
    </div>

    @if (session('status'))
        <div class="auth-session-status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="auth-session-status" style="color:#ef4444">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div class="field-group">
            <div class="auth-field anim-fade-up stagger-2">
                <label for="email" class="auth-field-label">{{ __('Correo electrónico') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="correo@empresa.com"
                    class="auth-field-input @error('email') error @enderror"
                >
                @error('email')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="anim-fade-up stagger-3">
            <button type="submit" class="auth-btn">
                <span class="shimmer"></span>
                <span class="text">{{ __('Enviar enlace de recuperación') }}</span>
            </button>
        </div>

        <p class="auth-form-footer anim-fade-up stagger-4">
            {{ __('¿Recordaste tu contraseña?') }}
            <a href="{{ route('login') }}" class="auth-link">
                {{ __('Inicia sesión') }}
            </a>
        </p>
    </form>
@endsection
