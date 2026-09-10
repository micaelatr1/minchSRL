@props([
    'title' => 'Sin registros',
    'description' => '',
    'action' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'text-center py-12']) }}>
    <x-icon :name="$icon" class="mx-auto h-12 w-12 text-gray-400 dark:text-dark-400" />
    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-dark-300">{{ $description }}</p>
    @endif
    @if ($action)
        <div class="mt-6">{{ $action }}</div>
    @endif
</div>
