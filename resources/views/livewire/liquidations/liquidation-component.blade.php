<div class="space-y-4">
    <x-card>
        <x-slot:header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span class="font-semibold text-base">Liquidaciones</span>
                <div class="flex items-center gap-2">
                    <x-input icon="magnifying-glass" placeholder="Buscar lote o proveedor..." wire:model.live.debounce.300ms="search" />
                    <x-select.styled wire:model.live="metalFilter" placeholder="Metal" :options="[
                        ['label' => 'Todos', 'value' => ''],
                        ['label' => 'Zn (Zinc)', 'value' => 'zn'],
                        ['label' => 'Pb (Plomo)', 'value' => 'pb'],
                    ]" />
                    <a href="{{ route('liquidation.form') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary-500 hover:bg-primary-600 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Nueva
                    </a>
                </div>
            </div>
        </x-slot:header>

        <div class="relative soft-scrollbar overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-600/20">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Lote</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Metal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Proveedor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Cooperativa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($liquidations as $liquidation)
                        <tr class="hover:bg-primary-500/5 transition-all duration-200">
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-mono text-dark-300">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-dark-100">{{ $liquidation->lote }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-dark-300">{{ $liquidation->date->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <x-badge :color="$liquidation->metal === 'zn' ? 'blue' : 'orange'">
                                    {{ $liquidation->metal === 'zn' ? 'Zn' : 'Pb' }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-dark-200">{{ $liquidation->full_name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-dark-400">{{ $liquidation->cooperative_name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('liquidation.form', $liquidation->id) }}"
                                       class="p-1.5 rounded-lg text-dark-400 hover:text-fuchsia-400 hover:bg-fuchsia-600/10 transition-all duration-200"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('liquidation.pdf', $liquidation->id) }}"
                                       class="p-1.5 rounded-lg text-dark-400 hover:text-emerald-400 hover:bg-emerald-600/10 transition-all duration-200"
                                       title="PDF" target="_blank">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('{{ $liquidation->id }}')"
                                            class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200 cursor-pointer"
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-dark-400">
                                No hay liquidaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="flex justify-center">
        {{ $liquidations->links('ts-ui::components.table.paginators') }}
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('¿Está seguro de eliminar esta liquidación?')) {
            @this.call('delete', id);
        }
    }
</script>
@endpush
