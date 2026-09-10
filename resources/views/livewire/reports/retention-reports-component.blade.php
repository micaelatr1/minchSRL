<div class="space-y-6">

    {{-- Título --}}
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reporte de Retenciones</h2>

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
                <div class="w-full sm:w-auto sm:min-w-[160px]">
                    <x-select.styled wire:model="tipo" label="Tipo" :options="[['label' => 'Todos', 'value' => ''], ['label' => 'Servicios', 'value' => 'S'], ['label' => 'Bienes', 'value' => 'G']]" class="w-full!" />
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
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Código</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Proveedor</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Tipo</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">NIT/CI</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Monto</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Dctos.</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($this->rows as $row)
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $row['code'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $row['date'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[200px] truncate">{{ $row['supplier'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">{{ $row['type'] == 'S' ? 'Servicios' : 'Bienes' }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">{{ $row['nit'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono text-dark-300">{{ number_format($row['amount'], 2, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono text-dark-300">{{ number_format($row['discounts'], 2, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">{{ number_format($row['total'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin datos</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay retenciones para el período seleccionado.</p>
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
                <span class="text-dark-200">Total Monto: {{ number_format(collect($this->rows)->sum('amount'), 2, ',', '.') }} Bs</span>
                <span class="text-dark-200">Total Dctos.: {{ number_format(collect($this->rows)->sum('discounts'), 2, ',', '.') }} Bs</span>
                <span class="text-primary-300">Total General: {{ number_format(collect($this->rows)->sum('total'), 2, ',', '.') }} Bs</span>
            </div>
        </div>
    @endif
</div>
