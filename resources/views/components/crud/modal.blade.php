@props([
    'entity' => '',
    'title' => null,
    'edit' => false,
    'modalId' => 'crud-modal',
    'saveMethod' => null,
    'storeMethod' => 'store',
    'updateMethod' => 'update',
    'offcanvas' => false,
])

@php
    $method = $saveMethod ?? ($edit ? $updateMethod : $storeMethod);
    $label = $title ?? $entity;
@endphp

@if ($offcanvas)
<div x-cloak
     x-data="tallstackui_modal(false, false)"
     x-show="show"
     x-on:modal:{{ $modalId }}-open.window="show = true"
     x-on:modal:{{ $modalId }}-close.window="show = false"
     x-on:close-modal.window="show = false"
     class="relative z-50">
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-400/75 dark:bg-dark-900/80 backdrop-blur-sm"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full justify-end p-0 sm:p-0">
            <div x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 class="relative flex w-full max-w-md transform flex-col bg-white dark:bg-dark-700 shadow-xl transition-all min-h-screen">
                <div class="flex items-center justify-between border-b border-b-gray-100 dark:border-b-dark-600 px-4 py-3">
                    <h3 class="text-md font-medium text-secondary-600 dark:text-dark-300 whitespace-normal">
                        {{ $edit ? 'Actualizar' : 'Nuevo' }} | {{ $label }}
                    </h3>
                    <button type="button" x-on:click="show = false" class="text-secondary-300 h-5 w-5 cursor-pointer hover:text-secondary-500 transition-colors">
                        <x-dynamic-component :component="TallStackUi::prefix('icon')" :icon="TallStackUi::icon('x-mark')" internal />
                    </button>
                </div>
                <div class="grow px-4 py-5 text-gray-700 dark:text-dark-300 overflow-y-auto soft-scrollbar">
                    <div class="space-y-2">
                        {{ $slot }}
                    </div>
                </div>
                <div class="sticky bottom-0 z-10 border-t border-t-gray-100 dark:border-t-dark-600 bg-white dark:bg-dark-700 px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-button color="outline" type="button" wire:click="clear()" icon="x-mark">Cancelar</x-button>
                        <x-button color="primary" wire:click="{{ $method }}" wire:loading.attr="disabled" wire:target="{{ $method }}" icon="check">
                            <span wire:loading.remove wire:target="{{ $method }}">
                                {{ $edit ? 'Actualizar' : 'Guardar' }}
                            </span>
                            <span wire:loading wire:target="{{ $method }}" class="flex items-center gap-2">
                                <x-ui.loading-spinner class="h-4 w-4" />
                                Guardando...
                            </span>
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div x-on:close-modal.window="$tsui.close.modal('{{ $modalId }}')">
    <x-modal :id="$modalId" persistent>
        <x-slot:header>
            <span class="font-semibold text-base">{{ $edit ? 'Actualizar' : 'Crear' }} | {{ $label }}</span>
        </x-slot:header>
        <div class="space-y-2">
            {{ $slot }}
        </div>
        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-button color="outline" type="button" wire:click="clear()" icon="x-mark">Cancelar</x-button>
                <x-button color="primary" wire:click="{{ $method }}" wire:loading.attr="disabled" wire:target="{{ $method }}" icon="check">
                    <span wire:loading.remove wire:target="{{ $method }}">
                        {{ $edit ? 'Actualizar' : 'Guardar' }}
                    </span>
                    <span wire:loading wire:target="{{ $method }}" class="flex items-center gap-2">
                        <x-ui.loading-spinner class="h-4 w-4" />
                        Guardando...
                    </span>
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
@endif
