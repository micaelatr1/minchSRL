<div class="space-y-6">
    {{-- Header: filtro + botón --}}
    <div class="flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-end bg-dark-800/40 backdrop-blur-sm rounded-xl p-4 border border-dark-600/20">
        <div class="flex flex-wrap gap-3 items-center">
            <x-date month-year-only wire:model="selectedDate" />
        </div>

        <div>
            <x-button color="primary" icon="magnifying-glass-circle" class="w-full sm:w-auto"
                wire:click.prevent="$refresh">Consultar</x-button>
        </div>
    </div>

    {{-- Grid de cuentas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach ($accounts as $account)
            <div class="group relative rounded-xl bg-dark-800/60 backdrop-blur-sm border border-dark-600/20 
                        hover:border-primary-500/30 hover:-translate-y-1 
                        transition-all duration-300 ease-out"
                 style="animation: card-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.08 }}s;">

                {{-- Glow sutil en hover --}}
                <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                     style="background: radial-gradient(ellipse at 50% 0%, rgba(37,99,235,0.06) 0%, transparent 70%);">
                </div>

                {{-- Header del card --}}
                <div class="relative px-5 py-4 border-b border-dark-600/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary-600/10 border border-primary-500/20">
                                <svg class="w-5 h-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 0 4.5 6h.75m13.5 0h.75a.75.75 0 0 0 .75-.75V4.5M4.5 18.75h15m-15 0V8.25a2.25 2.25 0 0 1 2.25-2.25h10.5A2.25 2.25 0 0 1 19.5 8.25v10.5"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-dark-400 uppercase tracking-wider">Nro. {{ $account->account_number }}</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" style="color:{{ $account->color }}">
                                    {{ $account->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cuerpo del card --}}
                <div class="relative px-5 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-dark-300">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6.75-6.75M12 19.5l6.75-6.75"/>
                            </svg>
                            <span>Debe</span>
                        </div>
                        <span class="text-sm font-semibold text-emerald-300">{{ number_format($account->amountD ?? 0, 2, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-dark-300">
                            <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75"/>
                            </svg>
                            <span>Haber</span>
                        </div>
                        <span class="text-sm font-semibold text-red-300">{{ number_format($account->amountC ?? 0, 2, ',', '.') }}</span>
                    </div>

                    <div class="pt-2 border-t border-dark-600/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm font-medium text-dark-300">
                                <svg class="w-4 h-4 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/>
                                </svg>
                                <span>Saldo</span>
                            </div>
                            <span class="text-sm font-bold px-3 py-1 rounded-lg"
                                  style="background:{{ $account->amountS <= 0 ? 'rgba(220,38,38,0.15)' : 'rgba(16,185,129,0.15)' }}; 
                                         color:{{ $account->amountS <= 0 ? '#fca5a5' : '#6ee7b7' }};
                                         border: 1px solid {{ $account->amountS <= 0 ? 'rgba(220,38,38,0.2)' : 'rgba(16,185,129,0.2)' }};">
                                {{ number_format($account->amountS, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Footer del card --}}
                <div class="relative px-5 py-3 border-t border-dark-600/20 flex justify-end">
                    <a href="{{ route('transactions.view', [$account->id, $this->selectedDate, 'supplier' => $this->selectedSupplierId]) }}"
                       class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-primary-400 hover:text-primary-300 transition-colors duration-200">
                        Ver movimientos
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Paginación --}}
    <div class="flex justify-center">
        {{ $accounts->links('ts-ui::components.table.paginators') }}
    </div>
</div>
