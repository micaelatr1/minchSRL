<div class="space-y-6">
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Volver
                </a>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Compras</h2>
            </div>
            @can('Crear entradas inventario')
                <x-button color="primary" icon="plus" :href="route('inventory.purchases.form')" wire:navigate>
                    Nueva Compra
                </x-button>
            @endcan
        </div>

        <div class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-64">
                <x-input icon="magnifying-glass" placeholder="Buscar por factura o proveedor..." wire:model.live.debounce.300ms="search" />
            </div>
            <div>
                <x-label>Desde</x-label>
                <x-date wire:model.live="dateFrom" format="YYYY-MM-DD" />
            </div>
            <div>
                <x-label>Hasta</x-label>
                <x-date wire:model.live="dateTo" format="YYYY-MM-DD" />
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative soft-scrollbar overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-600/20">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Proveedor</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Productos</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Total</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($purchases as $purchase)
                        <tr class="transition-all duration-200 hover:bg-primary-500/5">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $purchase->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3.5 text-sm text-dark-300 max-w-[200px] truncate" title="{{ $purchase->movement?->description ?? '' }}">
                                {{ $purchase->movement?->description ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200">{{ $purchase->supplier?->full_name }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-center text-sm text-dark-300">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    {{ $purchase->items->count() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">
                                {{ number_format($purchase->total, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('Editar entradas inventario')
                                        <x-button.circle color="blue" icon="pencil" :href="route('inventory.purchases.form', $purchase->id)"
                                            wire:navigate title="Editar" />
                                    @endcan
                                    @can('Eliminar entradas inventario')
                                        <x-button.circle color="red" icon="trash"
                                            onclick="confirmDelete('{{ $purchase->id }}', '{{ $purchase->invoice_number ?? 'S/N' }}')"
                                            title="Eliminar" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin compras registradas</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay compras en el período seleccionado.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-center">
        {{ $purchases->links() }}
    </div>
</div>

<script>
    function confirmDelete(id, invoiceNumber) {
        const component = Livewire.getByName('inventory.purchase-component')[0];

        $tsui.interaction('dialog')
            .wireable(component)
            .question('Advertencia', '¿Estás seguro de eliminar la compra ' + invoiceNumber + '?')
            .confirm('Confirmar', 'delete', [id])
            .cancel('Cancelar')
            .send();
    }
</script>
