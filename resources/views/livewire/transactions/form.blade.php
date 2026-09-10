<div x-on:close-modal.window="$tsui.close.modal('modal-id')">
    <x-modal title="modal-id" id="modal-id" persistent>
        <form id="user-create" wire:submit="save" class="space-y-2">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <x-input label="Ingrese el monto" placeholder="Monto" wire:model="amount" />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-select.styled label="Seleccione una cuenta" :options="$options" wire:model="account_id" />
                </div>
            </div>
            <div class="w-full">
                <x-input label="DCTO. REF." placeholder="Nro de documento" wire:model="doc" />
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <x-select.styled label="Seleccione tipo" :options="[
                        ['label' => 'Debe', 'value' => 'D'],
                        ['label' => 'Haber', 'value' => 'C'],
                    ]" wire:model='type' />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-date label="Seleccione una fecha" wire:model="date" />
                </div>
            </div>

            <div class="w-full">
                <x-textarea wire:model="description" label="Descripción" rows="5" />
            </div>
        </form>
        <x-slot:footer>
            <x-button color="outline" type="button" wire:click="clear()" icon="x-mark">
                Cancelar
            </x-button>
            @if ($this->id)
                <x-button color="primary" wire:click="update()" icon="check">
                    Actualizar
                </x-button>
            @else
                <x-button color="primary" wire:click="store()" icon="check">
                    Guardar
                </x-button>
            @endif
        </x-slot:footer>
    </x-modal>
</div>
