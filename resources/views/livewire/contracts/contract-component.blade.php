<div class="space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Contratos</h2>

    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-end">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div class="w-full sm:w-auto sm:min-w-[200px]">
                    <x-input wire:model.live.debounce.300ms="search" placeholder="Buscar por código, nombre o CI..." />
                </div>
                <div class="w-full sm:w-auto sm:min-w-[160px]">
                    <x-select.styled wire:model.live="statusFilter" :options="[
                        ['label' => 'Todos', 'value' => ''],
                        ['label' => 'En progreso', 'value' => 'in_progress'],
                        ['label' => 'Completado', 'value' => 'completed'],
                    ]" select="label:label|value:value" placeholder="Filtrar por estado" />
                </div>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                @can('Crear contratos')
                <a href="{{ route('contracts.form') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition-all duration-200 shadow-lg shadow-primary-600/20 justify-center sm:justify-start">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Nuevo contrato
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="relative">
        <x-ui.loading-spinner class="text-primary-500 dark:text-dark-300 absolute -top-4 left-0 right-0 m-auto h-10 w-10 z-10" wire:loading="search,statusFilter" />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" wire:loading.class="cursor-not-allowed select-none opacity-25">
            @forelse ($contracts as $contract)
                <div wire:key="{{ $contract->id }}"
                     class="group relative rounded-xl bg-dark-800/60 backdrop-blur-sm border border-dark-600/20
                            hover:border-primary-500/30 hover:-translate-y-1
                            transition-all duration-300 ease-out"
                     style="animation: card-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.08 }}s;">

                    {{-- Glow sutil en hover --}}
                    <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                         style="background: radial-gradient(ellipse at 50% 0%, rgba(37,99,235,0.06) 0%, transparent 70%);">
                    </div>

                    {{-- Header --}}
                    <div class="relative px-5 py-4 border-b border-dark-600/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary-600/10 border border-primary-500/20 shrink-0">
                                    <svg class="w-5 h-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-dark-400 uppercase tracking-wider">Contrato</p>
                                    <p class="text-sm font-semibold text-primary-400 font-mono truncate">{{ $contract->code }}</p>
                                </div>
                            </div>
                            <x-badge :color="$contract->type === 'supplier' ? 'orange' : 'blue'" class="shrink-0">
                                {{ $contract->type === 'supplier' ? 'Proveedor' : 'Cliente' }}
                            </x-badge>
                        </div>
                    </div>

                    {{-- Cuerpo --}}
                    <div class="relative px-5 py-4 space-y-3">
                        {{-- Persona --}}
                        <div class="flex items-center gap-2 text-sm text-dark-200">
                            <svg class="w-4 h-4 text-dark-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                            <span class="truncate" title="{{ $contract->person->full_name }}">{{ $contract->person->full_name }}</span>
                            <span class="text-xs font-mono text-dark-400 shrink-0">· CI: {{ $contract->person->ci }}</span>
                        </div>

                        {{-- Descripción --}}
                        @if($contract->description)
                            <p class="text-xs text-dark-400 leading-relaxed line-clamp-2" title="{{ $contract->description }}">
                                {{ $contract->description }}
                            </p>
                        @endif

                        {{-- Fechas --}}
                        <div class="flex items-center gap-4 text-xs text-dark-400">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                                </svg>
                                <span class="font-mono">{{ $contract->start_date->format('d/m/Y') }}</span>
                            </div>
                            @if($contract->end_date)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
                                    </svg>
                                    <span class="font-mono">{{ $contract->end_date->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Montos --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm text-dark-300">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6.75-6.75M12 19.5l6.75-6.75"/>
                                </svg>
                                <span>Pagado</span>
                            </div>
                            <span class="text-sm font-semibold text-emerald-300">{{ number_format((float) $contract->paid_amount, 2, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm text-dark-300">
                                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75"/>
                                </svg>
                                <span>Saldo</span>
                            </div>
                            <span class="text-sm font-semibold {{ $contract->remaining_amount > 0 ? 'text-red-300' : 'text-dark-400' }}">{{ number_format($contract->remaining_amount, 2, ',', '.') }}</span>
                        </div>

                        {{-- Progreso --}}
                        <div class="pt-2 border-t border-dark-600/20">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2 text-sm font-medium text-dark-300">
                                    <svg class="w-4 h-4 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/>
                                    </svg>
                                    <span>Progreso</span>
                                </div>
                                <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded-md"
                                      style="background:{{ $contract->progress >= 100 ? 'rgba(16,185,129,0.15)' : 'rgba(37,99,235,0.15)' }};
                                             color:{{ $contract->progress >= 100 ? '#6ee7b7' : '#93c5fd' }};
                                             border: 1px solid {{ $contract->progress >= 100 ? 'rgba(16,185,129,0.2)' : 'rgba(37,99,235,0.2)' }};">
                                    {{ $contract->progress }}%
                                </span>
                            </div>
                            <div class="h-2 rounded-full bg-dark-600 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: {{ $contract->progress }}%; background: {{ $contract->progress >= 100 ? '#22c55e' : '#3b82f6' }};">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="relative px-5 py-3 border-t border-dark-600/20 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('contracts.show', $contract->id) }}"
                               class="p-1.5 rounded-lg text-dark-400 hover:text-blue-400 hover:bg-blue-600/10 transition-all duration-200"
                               title="Ver detalle">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </a>
                            <a href="{{ route('contract.pdf', $contract->id) }}" target="_blank"
                               class="p-1.5 rounded-lg text-dark-400 hover:text-emerald-400 hover:bg-emerald-600/10 transition-all duration-200"
                               title="Descargar PDF">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                </svg>
                            </a>
                            <a href="{{ route('contracts.form', $contract->id) }}"
                               class="p-1.5 rounded-lg text-dark-400 hover:text-fuchsia-400 hover:bg-fuchsia-600/10 transition-all duration-200"
                               title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                            </a>
                            <button onclick="confirmDelete('{{ $contract->id }}')"
                                    class="p-1.5 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-600/10 transition-all duration-200 cursor-pointer"
                                    title="Eliminar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                    <div class="flex flex-col items-center justify-center gap-4 text-center py-16">
                        <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                            <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-dark-300">Sin contratos</p>
                            <p class="text-sm text-dark-400 mt-1">No hay contratos registrados.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="flex justify-center">
        {{ $contracts->links('ts-ui::components.table.paginators') }}
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('¿Está seguro de eliminar este contrato?')) {
            @this.call('delete', id);
        }
    }
</script>
@endpush
