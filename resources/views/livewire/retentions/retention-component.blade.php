<div class="space-y-6">

    {{-- Título --}}
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Retenciones</h2>

    {{-- Header: filtros + acciones --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">

        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-end">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div class="w-full sm:w-auto sm:min-w-[160px]">
                    <x-select.styled wire:model="type" :options="[['name' => 'Servicios', 'id' => 'S'], ['name' => 'Bienes', 'id' => 'G']]" select="label:name|value:id" required class="w-full!" />
                </div>
                <div class="w-full sm:w-auto sm:min-w-[160px]">
                    <x-date month-year-only wire:model="selectedDate" class="w-full!" />
                </div>
                <div class="w-full sm:w-auto">
                    <x-button color="primary" icon="magnifying-glass-circle" wire:click="consultar" class="w-full! sm:w-auto! justify-center">
                        Consultar
                    </x-button>
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <x-button color="primary" icon="plus" :href="route('retention.form')" wire:navigate class="w-full! sm:w-auto! justify-center sm:justify-start">
                    Agregar retención
                </x-button>
                <x-button color="emerald" icon="document-arrow-up" outline :href="route('retention.month.excel', [$this->selectedDate, $this->type])" class="w-full! sm:w-auto! justify-center sm:justify-start">
                    Descargar Excel
                </x-button>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="rounded-xl bg-dark-800/40 backdrop-blur-sm border border-dark-600/20 overflow-hidden">
        <div class="relative soft-scrollbar overflow-x-auto">
            <x-ui.loading-spinner class="text-primary-500 dark:text-dark-300 absolute bottom-0 left-0 right-0 top-0 m-auto h-10 w-10" wire:loading="quantity,search" />

            <table class="min-w-full divide-y divide-dark-600/10"
                wire:loading.class="cursor-not-allowed select-none opacity-25">

                <thead class="bg-dark-700/50">
                    <tr>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">
                            Nro
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">
                            Fecha
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">
                            Nombre y Apellido
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">
                            ROS
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">
                            Cédula de Identidad
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">
                            {{ $this->type == 'S' ? 'Servicio' : 'Bien' }}
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">
                            Monto
                        </th>
                        <th scope="col" colspan="{{ $taxes->count() }}"
                            class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300 border-b border-dark-600/10">
                            Retenciones
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-primary-400">
                            Importe a cancelar
                        </th>
                        <th scope="col" rowspan="3"
                            class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">
                            Opciones
                        </th>
                    </tr>

                    <tr class="bg-dark-700/30">
                        @foreach ($taxes as $taxe)
                            <th scope="col"
                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">
                                {{ $taxe->initials }}
                            </th>
                        @endforeach
                    </tr>
                    <tr class="bg-dark-700/20">
                        @foreach ($taxes as $taxe)
                            <th scope="col"
                                class="px-4 py-2 text-center text-[10px] font-mono text-dark-400">
                                {{ $taxe->number }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($retentions as $retention)
                        <tr class="group transition-all duration-200 hover:bg-primary-500/5"
                            wire:key="8b700ca168f875b5e2a3c9329ba84d55"
                            style="animation: fade-in-up 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.03 }}s;">
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">
                                {{ $loop->iteration }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">
                                {{ $retention->date }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-200 max-w-[180px] truncate" title="{{ $retention->supplier->full_name }}">
                                {{ $retention->supplier->full_name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">
                                {{ $retention->code }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm font-mono text-dark-300">
                                {{ $retention->supplier->ci }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-dark-300">
                                {{ $retention->summary }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono text-dark-300">
                                {{ $retention->amount_formatted }}
                            </td>
                            @foreach ($retention->discounts as $discount)
                                <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono text-dark-300">
                                    {{ $discount->amount_formatted }}
                                </td>
                            @endforeach
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-right font-mono font-semibold text-primary-300">
                                {{ $retention->calculate_total_formatted }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-sm text-center text-dark-300">
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-button.circle icon="document-arrow-down" color="red" light :href="route('retention.pdf.form', $retention->id)" target="_blank" title="Descargar PDF" />
                                    <x-button.circle icon="pencil" color="blue" light :href="route('retention.form', $retention->id)" wire:navigate title="Editar" />
                                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $retention->id }}')" title="Eliminar" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $taxes->count() + 9 }}" class="px-4 py-16">
                                <div class="flex flex-col items-center justify-center gap-4 text-center">
                                    <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                                        <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-dark-300">Sin retenciones</p>
                                        <p class="text-sm text-dark-400 mt-1">No hay retenciones registradas para este período.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{ $retentions->links() }}
</div>
