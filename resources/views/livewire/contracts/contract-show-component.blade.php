<div class="space-y-6">
    {{-- Encabezado --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('contracts') }}" wire:navigate
               class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('contract.pdf', $contract->id) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-emerald-300 bg-emerald-600/10 border border-emerald-500/20 hover:bg-emerald-600/20 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                PDF
            </a>
            @can('Editar contratos')
            <a href="{{ route('contracts.form', $contract->id) }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-fuchsia-300 bg-fuchsia-600/10 border border-fuchsia-500/20 hover:bg-fuchsia-600/20 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                </svg>
                Editar
            </a>
            @endcan
        </div>
    </div>

    {{-- Info del contrato --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold text-white">{{ $contract->code }}</h3>
                <p class="text-sm text-dark-400 mt-1">
                    {{ $contract->type === 'supplier' ? 'Proveedor' : 'Cliente' }}:
                    <span class="text-dark-200 font-semibold">{{ $contract->person->full_name }}</span>
                    &middot; CI: {{ $contract->person->ci }}
                </p>
            </div>
            <x-badge :color="$contract->status === 'completed' ? 'success' : 'info'" class="text-sm px-3 py-1">
                @switch($contract->status)
                    @case('in_progress') En progreso @break
                    @case('completed') Completado @break
                @endswitch
            </x-badge>
        </div>

        @if ($contract->description)
            <p class="text-sm text-dark-300">{{ $contract->description }}</p>
        @endif

        <div class="flex flex-wrap gap-6 text-sm">
            <div>
                <span class="text-dark-400">Inicio:</span>
                <span class="text-dark-200 font-semibold ml-1">{{ $contract->start_date->format('d/m/Y') }}</span>
            </div>
            @if ($contract->end_date)
            <div>
                <span class="text-dark-400">Fin:</span>
                <span class="text-dark-200 font-semibold ml-1">{{ $contract->end_date->format('d/m/Y') }}</span>
            </div>
            @endif
            @if ($contract->file)
            <div>
                <span class="text-dark-400">Archivo:</span>
                <a href="{{ asset('storage/' . $contract->file) }}" target="_blank"
                   class="text-blue-400 hover:text-blue-300 ml-1">Ver PDF</a>
            </div>
            @endif
        </div>

        {{-- Barra de progreso --}}
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-dark-400">Progreso de pago</span>
                <span class="text-dark-200 font-mono">{{ $contract->progress }}%</span>
            </div>
            <div class="w-full h-3 rounded-full bg-dark-600 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width: {{ $contract->progress }}%; background: linear-gradient(90deg, #3b82f6, {{ $contract->progress >= 100 ? '#22c55e' : '#06b6d4' }});">
                </div>
            </div>
        </div>

        {{-- Resumen financiero --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-dark-700/30 rounded-lg p-4 text-center">
                <span class="text-xs text-dark-400 uppercase tracking-wider">Total</span>
                <p class="text-xl font-bold text-white mt-1">Bs {{ number_format((float) $contract->total_amount, 2, ',', '.') }}</p>
            </div>
            <div class="bg-dark-700/30 rounded-lg p-4 text-center">
                <span class="text-xs text-dark-400 uppercase tracking-wider">Pagado</span>
                <p class="text-xl font-bold text-emerald-400 mt-1">Bs {{ number_format($contract->paid_amount, 2, ',', '.') }}</p>
            </div>
            <div class="bg-dark-700/30 rounded-lg p-4 text-center">
                <span class="text-xs text-dark-400 uppercase tracking-wider">Saldo pendiente</span>
                <p class="text-xl font-bold {{ $contract->remaining_amount > 0 ? 'text-red-400' : 'text-dark-400' }} mt-1">
                    Bs {{ number_format($contract->remaining_amount, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Pagos --}}
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-base font-semibold text-white">Pagos realizados</h3>
            @can('Crear contratos')
            @if ($contract->status !== 'completed')
            <x-button x-on:click="$tsui.open.modal('payment-modal')" icon="plus" color="primary">
                Registrar pago
            </x-button>
            @endif
            @endcan
        </div>

        @if ($contract->movements->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 py-10 text-center">
                <svg class="w-10 h-10 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p class="text-sm text-dark-400">No hay pagos registrados para este contrato.</p>
            </div>
        @else
            <div class="relative soft-scrollbar overflow-x-auto">
                <table class="min-w-full divide-y divide-dark-600/20">
                    <thead>
                        <tr class="bg-dark-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Nº</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-dark-300">Monto</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Tipo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Doc. Ref.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Descripción</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-300">Registrado por</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-600/10">
                        @foreach ($contract->movements as $movement)
                            <tr class="hover:bg-primary-500/5 transition-all duration-200">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-mono text-dark-300">{{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-dark-300">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-right font-mono text-emerald-400">Bs {{ number_format((float) $movement->amount, 2, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-center">
                                    @php
                                        $type = $movement->box ? 'cash_box' : ($movement->transaction ? 'bank' : 'direct');
                                    @endphp
                                    <x-badge :color="$type === 'cash_box' ? 'orange' : ($type === 'bank' ? 'blue' : 'gray')">
                                        {{ $this->getPaymentTypeLabel($type) }}
                                    </x-badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-center font-mono text-dark-300">
                                    {{ $this->getMovementRef($movement) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-dark-400 max-w-[200px] truncate" title="{{ $movement->description }}">
                                    {{ $movement->description ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-dark-400">{{ $movement->user?->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        @can('Editar contratos')
                                        <x-button.circle icon="pencil" color="blue" light
                                            x-on:click="
                                                $wire.set('editingMovementId', {{ $movement->id }});
                                                $wire.set('payment_amount', {{ (float) $movement->amount }});
                                                $wire.set('payment_date', '{{ $movement->date instanceof \Carbon\Carbon ? $movement->date->format('Y-m-d') : \Carbon\Carbon::parse($movement->date)->format('Y-m-d') }}');
                                                $wire.set('payment_description', '{{ addslashes($movement->description ?? '') }}');
                                                $wire.set('payment_type', '{{ $movement->box ? 'cash_box' : ($movement->transaction ? 'bank' : 'direct') }}');
                                                $wire.set('payment_method', '{{ $movement->transaction?->payment_type ?? 'CH' }}');
                                                $wire.set('number_check', '{{ $movement->transaction?->number_check ?? '' }}');
                                                $wire.set('account_id', {{ $movement->transaction?->account_id ?? 'null' }});
                                                $tsui.open.modal('payment-modal');
                                            " />
                                        @endcan
                                        @can('Eliminar contratos')
                                        <x-button.circle icon="trash" color="red" light
                                            onclick="confirmDelete('{{ $movement->id }}')" />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Modal: Registrar / Editar pago --}}
    <x-crud.modal entity="Pago" :edit="$editingMovementId" modal-id="payment-modal" save-method="store">
        <x-select.styled wire:model.live="payment_type" label="Tipo de pago" :options="[
            ['label' => 'Directo (solo movimiento)', 'value' => 'direct'],
            ['label' => 'Efectivo (Caja chica)', 'value' => 'cash_box'],
            ['label' => 'Banco (Transferencia / Cheque)', 'value' => 'bank'],
        ]" />

        <div class="flex flex-col sm:flex-row gap-2">
            <div class="w-full sm:w-1/2">
                <x-input label="Monto (Bs)" type="number" step="0.01" wire:model="payment_amount" placeholder="0.00" />
                @error('payment_amount') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="w-full sm:w-1/2">
                <x-date label="Fecha de pago" wire:model="payment_date" />
                @error('payment_date') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-input label="Descripción" wire:model="payment_description" placeholder="Concepto del pago" />
        @error('payment_description') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror

        @if ($payment_type === 'bank')
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="w-full sm:w-1/2">
                <x-select.styled wire:model="account_id" label="Cuenta bancaria" :options="$accounts->map(fn($a) => ['label' => $a->name . ' - ' . $a->number, 'value' => $a->id])->toArray()" />
                @error('account_id') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="w-full sm:w-1/2">
                <x-select.styled wire:model="payment_method" label="Método" :options="[
                    ['label' => 'Cheque', 'value' => 'CH'],
                    ['label' => 'Transferencia', 'value' => 'T'],
                ]" />
            </div>
        </div>
        <div class="w-full sm:w-1/2">
            <x-input label="Nro {{ $payment_method === 'T' ? 'de transferencia' : 'de cheque' }}" wire:model="number_check" placeholder="Opcional" />
            @error('number_check') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
        </div>
        @endif
    </x-crud.modal>
</div>

