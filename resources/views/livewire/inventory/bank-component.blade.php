<div class="space-y-6">
    {{-- Header --}}
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
            </div>
        </div>

        {{-- Account selector --}}
        @if ($inventoryAccounts->count() > 1)
            <div class="flex items-center gap-3">
                <x-select.styled wire:model.live="selectedAccountId" placeholder="Seleccionar cuenta..." :options="$inventoryAccounts->map(fn($a) => ['label' => $a->name . ' | ' . $a->account_number, 'value' => $a->id])->toArray()" />
            </div>
        @endif

        {{-- Account info --}}
        @if ($account)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-dark-700/40 border border-dark-600/20">
                    <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary-600/10 border border-primary-500/20">
                        <svg class="w-4 h-4 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 0 4.5 6h.75m13.5 0h.75a.75.75 0 0 0 .75-.75V4.5M4.5 18.75h15m-15 0V8.25a2.25 2.25 0 0 1 2.25-2.25h10.5A2.25 2.25 0 0 1 19.5 8.25v10.5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Banco Asignado</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $account->name }}</p>
                        <p class="text-xs text-dark-400">Nro. {{ $account->account_number }} | {{ $account->currency_type }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-dark-700/40 border border-dark-600/20">
                    <div><p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Departamento</p><p class="text-sm font-semibold text-gray-900 dark:text-white">Inventario</p></div>
                </div>
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-dark-700/40 border border-dark-600/20">
                    <div><p class="text-[10px] font-medium text-dark-400 uppercase tracking-wider">Periodo</p><p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedDate)->locale('es')->translatedFormat('F Y') }}</p></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 items-end justify-between">
                <div class="w-48">
                    <x-date month-year-only wire:model.live="selectedDate" />
                </div>
                <div class="flex gap-2">
                    @can('PDF banco inventario')
                        <x-button color="red" icon="document-arrow-down">PDF</x-button>
                    @endcan
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-dark-400">No tienes una cuenta bancaria asignada. Contacta al administrador.</p>
            </div>
        @endif
    </div>

    {{-- Transactions table --}}
    @if ($account)
        <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
            <div class="relative soft-scrollbar overflow-x-auto">
                <table class="min-w-full divide-y divide-dark-600/20">
                    <thead>
                        <tr class="bg-dark-700/50">
                            <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-emerald-400">Debe</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-red-400">Haber</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Saldo</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-600/10">
                        @forelse ($transactions as $transaction)
                                @php
                                $products = $this->getProductsForMovement($transaction->id);
                            @endphp
                            <tr class="transition-all duration-200 hover:bg-primary-500/5">
                                <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $transaction->transaction?->formatted_last_number ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $transaction->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3.5 text-sm text-dark-200 max-w-[250px]">
                                    <div class="truncate" title="{{ $transaction->description }}">{{ $transaction->description }}</div>
                                    @if ($products->isNotEmpty())
                                        <button wire:click="toggleExpand({{ $transaction->id }})"
                                                class="inline-flex items-center gap-1 text-xs text-primary-400 hover:text-primary-300 transition-colors mt-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/>
                                            </svg>
                                            {{ in_array($transaction->id, $expandedMovements) ? 'Ocultar' : 'Ver' }} productos
                                        </button>

                                        @if (in_array($transaction->id, $expandedMovements))
                                            <div class="mt-2 pl-2 border-l-2 border-primary-500/30 space-y-1">
                                                @foreach ($products as $item)
                                                    <div class="text-xs text-dark-300">
                                                        {{ $item->product->name }}
                                                        ({{ number_format(abs($item->quantity), 2, ',', '.') }} {{ $item->product->unit_label }}
                                                        x {{ number_format($item->unit_cost, 2, ',', '.') }})
                                                        = <span class="font-mono text-primary-300">{{ number_format($item->total_cost, 2, ',', '.') }} Bs</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
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
                                <td class="whitespace-nowrap px-4 py-3.5 text-center">
                                    @if ($transaction->type != 'B')
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('receipt.transaction.pdf', $transaction->id) }}"
                                               class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200"
                                               title="PDF">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                                </svg>
                                            </a>
                                            @can('Editar entradas inventario')
                                                <a href="{{ route('transactions.form', [$selectedDate, $selectedAccountId, $transaction->transaction?->id]) }}"
                                                   class="p-1.5 rounded-lg text-dark-400 hover:text-blue-400 hover:bg-blue-600/10 transition-all duration-200"
                                                   title="Editar">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                                    </svg>
                                                </a>
                                            @endcan
                                            @can('Eliminar entradas inventario')
                                                <button onclick="confirmDelete('{{ $transaction->id }}')"
                                                        class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200"
                                                        title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                    </svg>
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
                                            <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-base font-semibold text-dark-300">Sin movimientos</p>
                                            <p class="text-sm text-dark-400 mt-1">No hay movimientos en este período.</p>
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
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<script>
    function confirmDelete(id) {
        window.$wireui.confirm({
            title: '¿Eliminar movimiento?',
            description: 'Esta acción no se puede deshacer.',
            icon: 'error',
            accept: {
                label: 'Eliminar',
                color: 'danger',
                execute: () => window.Livewire.dispatch('delete::movement', { id })
            },
            reject: {
                label: 'Cancelar',
                color: 'secondary'
            }
        });
    }
</script>
