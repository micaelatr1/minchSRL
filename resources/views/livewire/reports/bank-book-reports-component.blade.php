<div class="space-y-6">

    {{-- Título --}}
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reporte de Libro de Bancos</h2>

    {{-- Header: filtros + acciones --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="w-full sm:w-auto sm:min-w-[140px]">
                    <x-date label="Fecha inicio" wire:model="fechaInicio" class="w-full!" />
                </div>
                <div class="w-full sm:w-auto sm:min-w-[140px]">
                    <x-date label="Fecha fin" wire:model="fechaFin" class="w-full!" />
                </div>
                <div class="w-full sm:w-auto sm:min-w-[200px]">
                    <x-select.styled wire:model="cuentaId" label="Cuenta" :options="$this->cuentaOptions" class="w-full!" />
                </div>
                <div class="w-full sm:w-auto">
                    <x-button color="primary" icon="magnifying-glass-circle" wire:click="$refresh" class="w-full! sm:w-auto! justify-center">
                        Consultar
                    </x-button>
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                @can('Exportar reportes')
                <x-button color="red" icon="document-arrow-down" wire:click="exportarPdf" class="w-full! sm:w-auto! justify-center">
                    PDF
                </x-button>
                <x-button color="emerald" icon="document-arrow-up" outline wire:click="exportarExcel" class="w-full! sm:w-auto! justify-center">
                    Excel
                </x-button>
                @endcan
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative overflow-auto soft-scrollbar">
            <table class="min-w-full divide-y divide-dark-600/10">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Dcto.Ref.</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-emerald-400">Débito</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-red-400">Crédito</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($this->rows as $row)
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $row['number'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $row['date'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[200px] truncate">{{ $row['description'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $row['ref'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-emerald-300">{{ $row['debito'] ? number_format($row['debito'], 2, ',', '.') : '' }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-medium text-red-300">{{ $row['credito'] ? number_format($row['credito'], 2, ',', '.') : '' }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v10.5m-7.5-3h15M3.75 12h16.5"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin datos</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay movimientos bancarios para el período seleccionado.</p>
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
        <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 px-5 py-4">
            <div class="flex flex-wrap justify-end gap-6 text-sm font-semibold">
                <span class="text-emerald-300">Total Débito: {{ number_format(collect($this->rows)->sum('debito'), 2, ',', '.') }} Bs</span>
                <span class="text-red-300">Total Crédito: {{ number_format(collect($this->rows)->sum('credito'), 2, ',', '.') }} Bs</span>
                <span class="text-primary-300">Saldo Final: {{ number_format(last($this->rows)['saldo'], 2, ',', '.') }} Bs</span>
            </div>
        </div>
    @endif
</div>
