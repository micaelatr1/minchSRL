@props([
    'title' => '',
    'createLabel' => 'Agregar',
    'modalId' => 'crud-modal',
    'createRoute' => null,
    'icon' => 'plus',
    'createPermission' => null,
])

<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    @if ($title)
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
    @endif
    <div class="flex gap-2 {{ $title ? '' : 'sm:justify-end w-full' }}">
        @if (!$createPermission || auth()->user()->can($createPermission))
            @if ($createRoute)
                <x-button :icon="$icon" :href="$createRoute" wire:navigate class="w-full sm:w-auto">
                    {{ $createLabel }}
                </x-button>
            @else
                <x-button :icon="$icon" x-on:click="$tsui.open.modal('{{ $modalId }}')" class="w-full sm:w-auto">
                    {{ $createLabel }}
                </x-button>
            @endif
        @endif
        {{ $extra ?? '' }}
    </div>
</div>
