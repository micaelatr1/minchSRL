<div class="space-y-6">

    {{-- Barra superior: volver + filtro + acciones --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">

        {{-- Fila 1: Volver --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounts') }}" wire:navigate
                   class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Volver
                </a>
            </div>
        </div>

        {{-- Fila 2: Filtro fecha + acciones --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <x-date month-year-only wire:model="selectedDate" />
                <x-button color="primary" icon="magnifying-glass-circle" wire:click="$refresh">Consultar</x-button>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('Crear caja chica')
                <a href="{{ route('accounts.box.form', ['date_account' => $selectedDate . '-01']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition-all duration-200 shadow-lg shadow-primary-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    Agregar movimiento
                </a>
                @endcan
                @can('PDF caja chica')
                <a href="{{ route('account.box.pdf', ['date' => $selectedDate]) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-red-300 bg-red-600/10 border border-red-500/20 hover:bg-red-600/20 transition-all duration-200">
                    <x-icon name="arrow-down-tray" class="w-4 h-4" />
                    Descargar PDF
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tabla de movimientos --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">

        <div class="relative soft-scrollbar overflow-x-auto">
            <x-ui.loading-spinner class="text-primary-500 dark:text-dark-300 absolute bottom-0 left-0 right-0 top-0 m-auto h-10 w-10" wire:loading="quantity,search" />

            <table class="min-w-full divide-y divide-dark-600/20"
                   wire:loading.class="cursor-not-allowed select-none opacity-25">

                <thead>
                    {{-- Cabecera columnas --}}
                    <tr class="bg-dark-700/50">
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-emerald-400">Debe</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-red-400">Haber</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Saldo</th>
                        <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Opciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($movements as $movement)
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            wire:key="8b700ca168f875b5e2a3c9329ba84d55"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">
                                {{ $movement->type == 'B' ? '' : ($movement->box?->number_label ?? '') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">
                                {{ $movement->date }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[200px] truncate" title="{{ $movement->description }}">
                                {{ $movement->description }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-emerald-300">
                                {{ $movement->type == 'D' ? number_format($movement->amount, 2, ',', '.') : ($movement->type == 'B' ? number_format($movement->amount, 2, ',', '.') : '') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-red-300">
                                {{ $movement->type == 'C' ? number_format($movement->amount, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">
                                {{ number_format($movement->balance, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center text-dark-300">
                                @if ($movement->type != 'B')
                                    <div class="flex items-center justify-center gap-1.5">
                                        @can('PDF caja chica')
                                        <a href="{{ route('receipt.box.pdf', $movement->id) }}"
                                           class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200"
                                           title="Descargar PDF">
                                            <x-icon name="document-arrow-down" class="w-4 h-4" />
                                        </a>
                                        @endcan
                                        @can('Editar caja chica')
                                        <a href="{{ route('accounts.box.form', ['id' => $movement->id, 'date_account' => $selectedDate . '-01']) }}"
                                           class="p-1.5 rounded-lg text-dark-400 hover:text-blue-400 hover:bg-blue-600/10 transition-all duration-200"
                                           title="Editar">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </a>
                                        @endcan
                                        @can('Eliminar caja chica')
                                        <button onclick="confirmDelete('{{ $movement->id }}')"
                                                class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200 cursor-pointer"
                                                title="Eliminar">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                        @endcan
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <x-icon name="document-text" class="w-8 h-8 text-dark-400" />
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin movimientos</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay movimientos registrados para este período.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-4 flex justify-center">
        <nav role="navigation" aria-label="Pagination Navigation">
            {{ $movements->links() }}
        </nav>
    </div>

</div>
