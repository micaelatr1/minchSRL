<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('inventory.products') }}" wire:navigate
                   class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Volver
                </a>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-button color="red" icon="document-arrow-down">PDF</x-button>
            </div>
        </div>

        {{-- Product info --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-dark-600/30 rounded-xl overflow-hidden border border-dark-600/20">
            <div class="bg-dark-800/60 px-4 py-3">
                <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Producto</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ $product->name }}</p>
            </div>
            <div class="bg-dark-800/60 px-4 py-3">
                <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Código</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ $product->code }}</p>
            </div>
            <div class="bg-dark-800/60 px-4 py-3">
                <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Categoría</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ $product->category_label }}</p>
            </div>
            <div class="bg-dark-800/60 px-4 py-3">
                <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Stock Actual</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit_label }}</p>
            </div>
            <div class="col-span-2 sm:col-span-4 bg-dark-800/60 px-4 py-3 border-t border-dark-600/20">
                <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Valor Inventario</p>
                <p class="text-sm font-bold text-primary-300 font-mono mt-0.5">Bs. {{ number_format($product->stock * $product->average_cost, 2, ',', '.') }}</p>
            </div>
        </div>

        {{-- Date filter --}}
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <x-label>Desde</x-label>
                <x-date wire:model="dateFrom" format="YYYY-MM-DD" />
            </div>
            <div>
                <x-label>Hasta</x-label>
                <x-date wire:model="dateTo" format="YYYY-MM-DD" />
            </div>
            <x-button color="primary" icon="magnifying-glass-circle" wire:click="$refresh">Consultar</x-button>
        </div>
    </div>

    {{-- Kardex table --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative soft-scrollbar overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-600/20">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Detalle</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-emerald-400">Entrada</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-red-400">Salida</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Costo Unit.</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Saldo Cant.</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Saldo Valor</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($movements as $movement)
                        <tr class="transition-all duration-200 hover:bg-primary-500/5">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $movement->date->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[200px] truncate" title="{{ $movement->notes }}">
                                {{ $movement->notes ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-emerald-300">
                                {{ $movement->quantity_in > 0 ? number_format($movement->quantity_in, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-red-300">
                                {{ $movement->quantity_out > 0 ? number_format($movement->quantity_out, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono text-dark-200">
                                {{ number_format($movement->unit_cost, 4, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono text-dark-200">
                                {{ number_format($movement->balance_quantity, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">
                                {{ number_format($movement->balance_total_value, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-center">
                                @php
                                    $canConsume = $movement->quantity_in > 0 && $movement->type === 'purchase';
                                @endphp
                                @if ($canConsume)
                                    <x-button.circle icon="arrow-up-tray" size="xs" color="indigo" light
                                        x-on:click="
                                            $wire.set('ec_movement_id', {{ $movement->id }});
                                            $wire.set('ec_date', '{{ now()->format('Y-m-d') }}');
                                            $wire.set('ec_quantity', '');
                                            $wire.set('ec_description', '');
                                            $tsui.open.modal('entry-consumption-modal')
                                        "
                                        title="Registrar salida desde esta entrada" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin movimientos</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay movimientos en el período seleccionado.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Ingreso --}}
    <x-modal name="entry-modal" wire:model="showEntryModal" title="Registrar Ingreso (Compra)">
        <form wire:submit="saveEntry" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input label="Cantidad" wire:model="entry_quantity" type="number" step="0.0001" required />
                    <span class="text-xs text-dark-400">{{ $product->unit_label }}</span>
                    @error('entry_quantity') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input label="Costo Unitario (Bs)" wire:model="entry_unit_cost" type="number" step="0.0001" required />
                    @error('entry_unit_cost') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-input label="Nro. Factura" wire:model="entry_document_number" required />
                    @error('entry_document_number') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-select.styled label="Proveedor" wire:model="entry_supplier_id" required
                        :options="\App\Models\Supplier::with('person')->get()->map(fn($s) => ['label' => $s->full_name . ' (' . $s->ci . ')', 'value' => $s->id])->toArray()" />
                    @error('entry_supplier_id') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-2">
                    <x-textarea label="Descripción" wire:model="entry_description" />
                </div>
            </div>
            <x-slot:footer>
                <x-button color="secondary" x-on:click="$wire.set('showEntryModal', false)">Cancelar</x-button>
                <x-button type="submit" color="primary" loading="saveEntry">Registrar Ingreso</x-button>
            </x-slot:footer>
        </form>
    </x-modal>

    {{-- Modal: Ajuste --}}
    <x-modal name="adjustment-modal" wire:model="showAdjustmentModal" title="Registrar Ajuste">
        <form wire:submit="saveAdjustment" class="space-y-4">
            <p class="text-sm text-dark-300">Stock actual: <strong>{{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit_label }}</strong></p>
            <x-input label="Cantidad (+/-)" wire:model="adj_quantity" type="number" step="0.0001" required
                     hint="Usa + para aumentar, - para disminuir" />
            @error('adj_quantity') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            <x-input label="Costo Unitario (solo para ajuste positivo)" wire:model="adj_unit_cost" type="number" step="0.0001" />
            <x-textarea label="Descripción / Motivo" wire:model="adj_description" />
            <x-slot:footer>
                <x-button color="secondary" x-on:click="$wire.set('showAdjustmentModal', false)">Cancelar</x-button>
                <x-button type="submit" color="secondary" loading="saveAdjustment">Registrar Ajuste</x-button>
            </x-slot:footer>
        </form>
    </x-modal>

    {{-- Modal: Salida desde entrada --}}
    <x-crud.modal title="Salida desde Entrada" modal-id="entry-consumption-modal" save-method="saveEntryConsumption">
        @php $ecMovement = $ec_movement_id ? \App\Models\KardexMovement::find($ec_movement_id) : null; @endphp
        @if ($ecMovement)
            <div class="rounded-lg bg-dark-700/30 border border-dark-600/20 p-3 space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-dark-400">Entrada #{{ $ecMovement->id }}</span>
                    <span class="text-dark-300">{{ $ecMovement->date?->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-dark-400">Cantidad original:</span>
                    <span class="font-mono text-emerald-300">{{ number_format($ecMovement->quantity_in, 2, ',', '.') }} {{ $product->unit_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-dark-400">Costo unitario:</span>
                    <span class="font-mono">{{ number_format($ecMovement->unit_cost, 4, ',', '.') }} Bs</span>
                </div>
                @if ($ecMovement->notes)
                    <div class="flex justify-between">
                        <span class="text-dark-400">Detalle:</span>
                        <span class="text-dark-200">{{ $ecMovement->notes }}</span>
                    </div>
                @endif
            </div>
        @endif
        <p class="text-sm text-dark-300">
            Stock actual: <strong>{{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit_label }}</strong>
        </p>
        <div class="grid grid-cols-2 gap-4">
            <x-input label="Cantidad a salir" wire:model="ec_quantity" type="number" step="0.0001" required />
            <x-input label="Fecha" wire:model="ec_date" type="date" required />
        </div>
        @error('ec_quantity') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
        @error('ec_date') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
        <x-textarea label="Descripción" wire:model="ec_description" />
    </x-crud.modal>
</div>
