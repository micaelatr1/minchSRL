<div class="space-y-6">

    {{-- Título --}}
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reporte de Liquidaciones</h2>

    {{-- Header --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary-600/10 border border-primary-500/20">
                <svg class="w-4 h-4 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Zm0 9.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25Zm9.75 0A2.25 2.25 0 0 1 15.75 13.5H18a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Zm0-9.75A2.25 2.25 0 0 1 15.75 3.75H18a2.25 2.25 0 0 1 2.25 2.25v2.25a2.25 2.25 0 0 1-2.25 2.25h-2.25a2.25 2.25 0 0 1-2.25-2.25V6Z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Reporte de Liquidaciones</p>
                <p class="text-xs text-dark-400">Seleccione un período para visualizar los datos.</p>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative overflow-auto soft-scrollbar">
            <table class="min-w-full divide-y divide-dark-600/10">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">N°</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Cooperativa</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Au (g)</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Ag (g)</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">Total Bs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    <tr>
                        <td colspan="6" class="px-4 py-16">
                            <div class="flex flex-col items-center justify-center gap-4 text-center">
                                <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                    <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Zm0 9.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25Zm9.75 0A2.25 2.25 0 0 1 15.75 13.5H18a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Zm0-9.75A2.25 2.25 0 0 1 15.75 3.75H18a2.25 2.25 0 0 1 2.25 2.25v2.25a2.25 2.25 0 0 1-2.25 2.25h-2.25a2.25 2.25 0 0 1-2.25-2.25V6Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-dark-300">Sin datos</p>
                                    <p class="text-sm text-dark-400 mt-1">No hay liquidaciones disponibles.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
