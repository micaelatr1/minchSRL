<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name', 'SIC-MINCH') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite('resources/js/app.js')
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
        .error-gradient { background: linear-gradient(135deg, #0f1724 0%, #152032 50%, #1e293b 100%); }
    </style>
</head>
<body class="antialiased error-gradient min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="bg-[#152032] dark:bg-[#152032] shadow-2xl ring-1 ring-[#334155] overflow-hidden rounded-2xl p-8 text-center">
            <div class="mb-6">
                <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#1d4ed8]/20 text-[#4a94ff]">
                    @yield('icon')
                </span>
            </div>
            <h1 class="text-6xl font-bold text-white mb-2">@yield('code')</h1>
            <h2 class="text-xl font-semibold text-[#cbd5e1] mb-4">@yield('message')</h2>
            <p class="text-sm text-[#64748b] mb-8">@yield('description')</p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#334155] text-[#e2e8f0] hover:bg-[#475569] transition-colors text-sm font-medium no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver atrás
                </a>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#1d4ed8] text-white hover:bg-[#2563eb] transition-colors text-sm font-medium no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Ir al inicio
                </a>
            </div>
        </div>
        <p class="text-center text-xs text-[#475569] mt-6">{{ config('app.name', 'SIC-MINCH') }} &mdash; Sistema de Gestión</p>
    </div>
</body>
</html>
