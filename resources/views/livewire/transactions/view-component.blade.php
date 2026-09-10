<div class="space-y-6">

    {{-- Barra superior: volver + info cuenta + acciones --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">

        {{-- Fila 1: Volver + título --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('transactions') }}" wire:navigate
                   class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                    Volver
                </a>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @can('Crear movimientos de libro de bancos')
                    <a href="{{ route('transactions.form', [$this->date, $this->idAccount]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition-all duration-200 shadow-lg shadow-primary-600/20">
                        <x-icon name="plus" class="w-4 h-4" />
                        Agregar movimiento
                    </a>
                @endcan
            </div>
        </div>

        {{-- Fila 2: Datos de la cuenta --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-dark-700/40 border border-dark-600/20">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary-600/10 border border-primary-500/20">
                    <x-icon name="building-library" class="w-4 h-4 text-primary-400" />
                </div>
                <div>
                    <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Cuenta</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->account }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-dark-700/40 border border-dark-600/20">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-600/10 border border-emerald-500/20">
                    <x-icon name="currency-dollar" class="w-4 h-4 text-emerald-400" />
                </div>
                <div>
                    <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Moneda</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->moneda }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-dark-700/40 border border-dark-600/20">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-purple-600/10 border border-purple-500/20">
                    <x-icon name="calendar" class="w-4 h-4 text-purple-400" />
                </div>
                <div>
                    <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Periodo</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->dateT }}</p>
                </div>
            </div>
        </div>

        {{-- Fila 3: Botones de exportación --}}
        <div class="flex flex-wrap gap-3 justify-end">
            @can('PDF libro de bancos')
                <a href="{{ route('transaction.account.pdf', [$this->start, $this->end, $this->idAccount]) }}" target="_bank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-red-300 bg-red-600/10 border border-red-500/20 hover:bg-red-600/20 transition-all duration-200">
                    <x-icon name="arrow-down-tray" class="w-4 h-4" />
                    PDF
                </a>
            @endcan
            @can('Excel libro de bancos')
                <a href="{{ route('transaction.account.excel', [$this->start, $this->end, $this->idAccount]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-emerald-300 bg-emerald-600/10 border border-emerald-500/20 hover:bg-emerald-600/20 transition-all duration-200">
                    <x-icon name="arrow-down-tray" class="w-4 h-4" />
                    Excel
                </a>
            @endcan
        </div>
    </div>

    {{-- Tabla de movimientos --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">

        <div class="relative soft-scrollbar overflow-x-auto">
            <x-ui.loading-spinner class="text-primary-500 dark:text-dark-300 absolute bottom-0 left-0 right-0 top-0 m-auto h-10 w-10" wire:loading="quantity,search" />

            <table class="min-w-full divide-y divide-dark-600/20"
                   wire:loading.class="cursor-not-allowed select-none opacity-25">

                {{-- Cabecera columnas --}}
                    <tr class="bg-dark-700/50">
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Dcto. Ref.</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-emerald-400">Debe</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-red-400">Haber</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Saldo</th>
                        <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Opciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($transactions as $transaction)
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            wire:key="8b700ca168f875b5e2a3c9329ba84d55"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">
                                {{ $transaction->transaction->formatted_last_number }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">
                                {{ $transaction->date }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[200px] truncate" title="{{ $transaction->description }}">
                                {{ $transaction->description }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300 font-mono">
                                {{ $transaction->transaction->number_label }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-emerald-300">
                                {{ $transaction->type == 'D' || $transaction->type == 'B' ? number_format($transaction->amount, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-red-300">
                                {{ $transaction->type == 'C' ? number_format($transaction->amount, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">
                                {{ number_format($transaction->balance, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center text-dark-300">
                                @if ($transaction->type != 'B')
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('receipt.transaction.pdf', $transaction->id) }}" target="_bank"
                                           class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200"
                                           title="Descargar PDF">
                                            <x-icon name="document-arrow-down" class="w-4 h-4" />
                                        </a>
                                        @can('Editar movimientos de libro de bancos')
                                            <a href="{{ route('transactions.form', [$this->date, $this->idAccount, $transaction->id]) }}"
                                               class="p-1.5 rounded-lg text-dark-400 hover:text-blue-400 hover:bg-blue-600/10 transition-all duration-200"
                                               title="Editar">
                                                <x-icon name="pencil-square" class="w-4 h-4" />
                                            </a>
                                        @endcan
                                        @can('Eliminar movimientos de libro de bancos')
                                            <button onclick="confirmDelete('{{ $transaction->id }}')"
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
                            <td colspan="8" class="px-4 py-16">
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
            {{ $transactions->links() }}
        </nav>
    </div>
</div>
