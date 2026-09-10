<div class="space-y-6">

    {{-- Barra superior: volver + acciones --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('accounts.statement') }}" wire:navigate
               class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
            <div class="flex flex-wrap gap-2">
                @can('Crear estados de cuenta')
                    <button x-on:click="$tsui.open.modal('crud-modal')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition-all duration-200 shadow-lg shadow-primary-600/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar movimiento
                    </button>
                @endcan
                @can('PDF estados de cuenta')
                    <a href="{{ route('account.statement.pdf', ['type' => $this->holderType, 'id' => $this->holderId]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-red-300 bg-red-600/10 border border-red-500/20 hover:bg-red-600/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Descargar PDF
                    </a>
                @endcan
            </div>
        </div>

        {{-- Info del titular --}}
        <div class="flex flex-col gap-2">
            <p class="text-xs font-medium text-dark-400 uppercase tracking-wider">{{ $holderLabel }}</p>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $holder->full_name }}</h1>
            <p class="text-sm text-dark-400">CI: {{ $holder->ci }}</p>
        </div>

        {{-- Cards Debe / Haber / Saldo --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-dark-700/40 border border-dark-600/20">
                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-emerald-600/10 border border-emerald-500/20">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6.75-6.75M12 19.5l6.75-6.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Debe</p>
                    <p class="text-sm font-bold text-emerald-300">Bs {{ $holder->amountD }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-dark-700/40 border border-dark-600/20">
                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-red-600/10 border border-red-500/20">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Haber</p>
                    <p class="text-sm font-bold text-red-300">Bs {{ $holder->amountC }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-dark-700/40 border border-dark-600/20">
                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary-600/10 border border-primary-500/20">
                    <svg class="w-5 h-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Saldo</p>
                    <p class="text-sm font-bold text-primary-300">Bs {{ $holder->balance }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative soft-scrollbar overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-600/10">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Dcto. Ref.</th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº Volq.</th>
                        <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Contrato</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-emerald-400">Debe</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-red-400">Haber</th>
                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Saldo</th>
                        <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Opciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($accountStatements as $movement)
                        @php
                            $documentRef = $movement->box?->number_label
                                ?? $movement->transaction?->number_label
                                ?? '';
                            $isManual = ! $movement->box && ! $movement->transaction && $movement->type !== 'B';
                        @endphp
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            wire:key="movement-{{ $movement->id }}"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $movement->date }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[200px] truncate" title="{{ $movement->description }}">{{ $movement->description }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $documentRef }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $movement->number_vol }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center">
                                @if ($movement->contract)
                                    <a href="{{ route('contracts.show', $movement->contract_id) }}"
                                       class="text-blue-400 hover:text-blue-300 font-mono text-xs">
                                        {{ $movement->contract->code }}
                                    </a>
                                @else
                                    <span class="text-dark-500">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-emerald-300">
                                {{ in_array($movement->type, ['D', 'B']) ? number_format($movement->amount, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-red-300">
                                {{ $movement->type === 'C' ? number_format($movement->amount, 2, ',', '.') : '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">
                                {{ number_format($movement->balance, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center text-dark-300">
                                @if ($movement->type !== 'B' && $isManual)
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button wire:click="$dispatch('load::movement', { movement: {{ $movement->id }} })"
                                                class="p-1.5 rounded-lg text-dark-400 hover:text-blue-400 hover:bg-blue-600/10 transition-all duration-200 cursor-pointer"
                                                title="Editar">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $movement->id }})"
                                                wire:confirm="¿Está seguro de eliminar este movimiento?"
                                                class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200 cursor-pointer"
                                                title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin movimientos</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay movimientos registrados para este {{ strtolower($holderLabel) }}.</p>
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
        {{ $accountStatements->links() }}
    </div>

</div>
