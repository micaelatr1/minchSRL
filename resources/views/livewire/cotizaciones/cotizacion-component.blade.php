<div>
    <x-card title="Cotizaciones de Minerales">
        <x-slot:header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <select wire:model.live="filterMetal"
                        class="block w-48 rounded-lg border-gray-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Todas las cotizaciones</option>
                        @foreach ($this->availableMetals as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    @if ($lastUpdate)
                        <span class="text-xs text-gray-500 dark:text-dark-300">
                            Última actualización: {{ $lastUpdate }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <x-button icon="arrow-path" color="primary" wire:click="refresh" wire:loading.attr="disabled">
                        Actualizar
                    </x-button>
                </div>
            </div>
        </x-slot:header>

        <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity duration-200">
            @if (count($this->filteredPrices) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-dark-600">
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-dark-200">Metal</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-dark-200 text-right">Precio</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-dark-200">Unidad</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-dark-200">Fuente</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 dark:text-dark-200">Actualización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-dark-600">
                            @foreach ($this->filteredPrices as $quote)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $quote['metalName'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($quote['price'], 4) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-dark-300">
                                        {{ $quote['unit'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($quote['source'] === 'SENARECOM')
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-300">
                                                SENARECOM
                                                <span class="text-[10px] opacity-70">Oficial</span>
                                            </span>
                                        @elseif ($quote['source'] === 'Kitco')
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:text-yellow-300">
                                                Kitco
                                                <span class="text-[10px] opacity-70">Spot</span>
                                            </span>
                                        @elseif ($quote['source'] === 'PortalMinero')
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-orange-100 dark:bg-orange-900/30 px-2.5 py-0.5 text-xs font-medium text-orange-800 dark:text-orange-300">
                                                PortalMinero
                                                <span class="text-[10px] opacity-70">LME</span>
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-300">
                                                {{ $quote['source'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-dark-400">
                                        {{ \Carbon\Carbon::parse($quote['updatedAt'])->isoFormat('DD/MM/YYYY HH:mm') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-dark-400">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm">No hay cotizaciones disponibles</p>
                    <x-button icon="arrow-path" color="secondary" wire:click="refresh" class="mt-3" size="sm">
                        Intentar de nuevo
                    </x-button>
                </div>
            @endif
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-dark-500">
                <span>Fuentes: SENARECOM (oficial Bolivia) &bull; Kitco (spot internacional) &bull; PortalMinero (LME)</span>
                <span wire:loading wire:target="refresh" class="text-primary-500">
                    Actualizando...
                </span>
            </div>
        </x-slot:footer>
    </x-card>
</div>
