<div class="space-y-6">

    @php
        $f = fn ($v, $d = 2) => number_format((float) $v, $d, ',', '.');
        $head = 'px-3 py-3 text-xs font-semibold uppercase tracking-wider text-dark-300';
        $headR = $head.' text-right';
        $cell = 'px-3 py-3 text-sm text-dark-300 whitespace-nowrap';
        $cellR = $cell.' text-right font-mono';
    @endphp

    {{-- Título --}}
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cuadro de Producción — Liquidaciones</h2>

    {{-- Header: filtros + acciones --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="w-full sm:w-auto sm:min-w-[180px]">
                    <x-select.styled wire:model.live="mes" label="Mes" :options="$this->mesOptions" class="w-full!" />
                </div>
                <div class="w-full sm:w-auto sm:min-w-[180px]">
                    <x-select.styled wire:model.live="metal" label="Metal" :options="$this->metalOptions" class="w-full!" />
                </div>
                <div class="w-full sm:w-auto">
                    <x-button color="primary" icon="magnifying-glass-circle" wire:click="$refresh" class="w-full! sm:w-auto! justify-center">
                        Consultar
                    </x-button>
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                @can('Exportar reportes')
                <x-button color="emerald" icon="document-arrow-up" outline wire:click="exportarExcel" class="w-full! sm:w-auto! justify-center">
                    Excel
                </x-button>
                @endcan
            </div>
        </div>
        <p class="mt-3 text-xs text-dark-400">Período: {{ $this->periodo }} · {{ count($this->rows) }} liquidación(es)</p>
    </div>

    {{-- Tabla --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative overflow-auto soft-scrollbar">
            <table class="min-w-full divide-y divide-dark-600/10">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">N°</th>
                        <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº Lote</th>
                        <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Proveedor</th>
                        <th colspan="4" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-emerald-300 border-l border-dark-600/30">Pesos</th>
                        <th colspan="3" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-emerald-300 border-l border-dark-600/30">Términos</th>
                        <th colspan="5" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-emerald-300 border-l border-dark-600/30">Fijación Precios</th>
                        <th rowspan="2" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-primary-400 border-l border-dark-600/30">Valor Bruto<br>USD</th>
                        <th colspan="8" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-red-300 border-l border-dark-600/30">Deducciones (Bs)</th>
                        <th colspan="2" class="px-3 py-2 text-center text-xs font-bold uppercase tracking-wider text-primary-300 border-l border-dark-600/30">Resultado (Bs)</th>
                    </tr>
                    <tr class="bg-dark-700/50">
                        <th class="{{ $headR }} border-l border-dark-600/30">Peso Bruto TMH</th>
                        <th class="{{ $headR }}">Humedad</th>
                        <th class="{{ $headR }}">Merma</th>
                        <th class="{{ $headR }}">Peso Neto TMS</th>
                        <th class="{{ $headR }} border-l border-dark-600/30">Ag (g/t)</th>
                        <th class="{{ $headR }}">Pb</th>
                        <th class="{{ $headR }}">Maq.</th>
                        <th class="{{ $headR }} border-l border-dark-600/30">Base</th>
                        <th class="{{ $headR }}">Ref.</th>
                        <th class="{{ $headR }}">Esc.</th>
                        <th class="{{ $headR }}">Pb</th>
                        <th class="{{ $headR }}">Ag</th>
                        <th class="{{ $headR }} border-l border-dark-600/30">RM. Ag.</th>
                        <th class="{{ $headR }}">RM. Pb</th>
                        <th class="{{ $headR }}">C.N.S.</th>
                        <th class="{{ $headR }}">COMIBOL</th>
                        <th class="{{ $headR }}">FEDECOMIN</th>
                        <th class="{{ $headR }}">FENCOMIN</th>
                        <th class="{{ $headR }}">Aporte Coop.</th>
                        <th class="{{ $headR }}">Total Deducc</th>
                        <th class="{{ $headR }} border-l border-dark-600/30">Liquido Pagable</th>
                        <th class="{{ $headR }}">Valor Liq.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($this->rows as $row)
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="{{ $cell }} font-mono text-dark-400">{{ $row['n'] }}</td>
                            <td class="{{ $cell }}">{{ $row['date'] }}</td>
                            <td class="{{ $cell }} font-mono text-dark-200">{{ $row['lote'] }}</td>
                            <td class="{{ $cell }} text-dark-200 max-w-[180px] truncate" title="{{ $row['proveedor'] }}">{{ $row['proveedor'] }}</td>
                            <td class="{{ $cellR }} border-l border-dark-600/30">{{ $f($row['tmh'], 3) }}</td>
                            <td class="{{ $cellR }} text-dark-400">{{ $f($row['humedad']) }}</td>
                            <td class="{{ $cellR }} text-dark-400">{{ $f($row['merma']) }}</td>
                            <td class="{{ $cellR }} font-semibold text-emerald-300">{{ $f($row['tmns'], 3) }}</td>
                            <td class="{{ $cellR }} border-l border-dark-600/30">{{ $f($row['agGt']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['pbPct']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['maquila']) }}</td>
                            <td class="{{ $cellR }} border-l border-dark-600/30">{{ $f($row['base']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['ref'], 3) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['esc']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['pb']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['ag']) }}</td>
                            <td class="{{ $cellR }} border-l border-dark-600/30 text-red-300">{{ $f($row['rmAg']) }}</td>
                            <td class="{{ $cellR }} text-red-300">{{ $f($row['rmPb']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['cns']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['comibol']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['fedecomin']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['fencomin']) }}</td>
                            <td class="{{ $cellR }}">{{ $f($row['aporteCoop']) }}</td>
                            <td class="{{ $cellR }} font-semibold">{{ $f($row['totalDeducc']) }}</td>
                            <td class="{{ $cellR }} font-semibold text-primary-300 border-l border-dark-600/30">{{ $f($row['liquidoPagable']) }}</td>
                            <td class="{{ $cellR }} font-semibold text-primary-300">{{ $f($row['valorLiq']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="27" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5a3.375 3.375 0 0 1-3.375-3.375V5.25m0 0A2.25 2.25 0 0 0 10.5 3H6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25V5.25m-9.75 0h7.5"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin datos</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay liquidaciones para {{ $this->periodo }}.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer: totales --}}
    @if (count($this->rows) > 0)
        @php $t = $this->totals; @endphp
        <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 px-5 py-4">
            <div class="flex flex-wrap justify-end gap-x-6 gap-y-2 text-sm font-semibold">
                <span class="text-emerald-300">TMH: {{ $f($t['tmh'], 3) }}</span>
                <span class="text-emerald-300">TMS: {{ $f($t['tmns'], 3) }}</span>
                <span class="text-primary-300">Valor Bruto: USD {{ $f($t['valorBrutoUsd']) }}</span>
                <span class="text-red-300">Total Deducc: Bs {{ $f($t['totalDeducc']) }}</span>
                <span class="text-primary-300">Liquido Pagable: Bs {{ $f($t['liquidoPagable']) }}</span>
                <span class="text-primary-300">Valor Liq.: Bs {{ $f($t['valorLiq']) }}</span>
            </div>
        </div>
    @endif
</div>
