<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <tallstackui:script />
        @livewireStyles
        @vite('resources/js/app.js')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-dark-900">

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white dark:bg-dark-800 shadow-lg ring-1 ring-gray-200 dark:ring-dark-700 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
