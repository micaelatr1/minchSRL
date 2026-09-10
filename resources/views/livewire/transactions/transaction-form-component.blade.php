<div class="space-y-4">
    <x-card>
        <x-slot:header>
            <span class="font-semibold text-base">Registro de Movimiento</span>
        </x-slot:header>
        <form id="user-create" wire:submit="{{ $this->id ? 'update' : 'store' }}" class="space-y-2" x-on:keydown.cmd.enter="$wire.{{ $this->id ? 'update' : 'store' }}()">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/3">
                    <x-select.styled wire:model.live="personType" label="Tipo de persona" :options="[
                        ['label' => 'Proveedor', 'value' => 'supplier'],
                        ['label' => 'Cliente', 'value' => 'customer'],
                    ]" />
                </div>
                <div class="w-full sm:w-2/3">
                    <livewire:supplier-search :ci="$ci" />
                </div>
            </div>
            @if (count($contracts) > 0)
                <div class="w-full">
                    <x-select.styled
                        wire:model="contract_id"
                        label="Contrato asociado (opcional)"
                        placeholder="Sin contrato"
                        :options="collect($contracts)->map(fn($c) => [
                            'label' => $c->code . ' - Saldo: Bs ' . number_format($c->remaining_amount, 2),
                            'value' => $c->id
                        ])->toArray()"
                    />
                </div>
            @endif
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <x-input label="Nombre y Apellido" wire:model="full_name" placeholder="Nombre completo" />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-input label="Teléfono" wire:model="phone" placeholder="Teléfono" />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <x-input label="Ingrese el monto" placeholder="Monto" wire:model="amount" />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-select.styled label="Método de pago" :options="[['label' => 'Cheque', 'value' => 'CH'], ['label' => 'Transferencia', 'value' => 'T']]" wire:model='payment_type' />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <x-input label="{{ $payment_type === 'T' ? 'Nro de transferencia' : 'Nro de cheque' }}" placeholder="{{ $payment_type === 'T' ? 'Nro de transferencia' : 'Nro de cheque' }}" wire:model="number_check" />
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <x-select.styled label="Seleccione tipo" :options="[['label' => 'Debe', 'value' => 'D'], ['label' => 'Haber', 'value' => 'C']]" wire:model='type' />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-date label="Seleccione una fecha" wire:model="date" name="date" />
                </div>
            </div>
            <div class="w-full">
                <x-textarea wire:model="description" label="Descripción" rows="5" />
            </div>
            <div class="flex flex-col sm:flex-row gap-4 sm:justify-end sm:items-end">
                <x-button color="outline" type="button" wire:navigate href="{{ route('transactions.view', ['id' => $account_id, 'date' => $date_account]) }}" icon="x-mark">
                    Cancelar
                </x-button>
                <x-button color="primary" wire:click="{{ $this->id ? 'update' : 'store' }}" wire:loading.attr="disabled" wire:target="store,update" icon="check">
                    <span wire:loading.remove wire:target="store,update">
                        {{ $this->id ? 'Actualizar' : 'Guardar' }}
                    </span>
                    <span wire:loading wire:target="store,update" class="flex items-center gap-2">
                        <x-ui.loading-spinner class="h-4 w-4" />
                        Guardando...
                    </span>
                </x-button>
            </div>
        </form>
    </x-card>
</div>