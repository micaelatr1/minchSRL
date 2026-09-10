<x-crud.modal entity="Cuenta" :edit="$this->id">
    <x-input label="Nombre" placeholder="Nombre" wire:model="name" />
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-1/2">
            <x-input label="Numero de Cuenta" type="text" placeholder="Numero de cuenta" wire:model="account_number" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-select.styled label="Seleccione el tipo de moneda" :options="[['label' => 'BOB', 'value' => 'BOB'], ['label' => 'EUR', 'value' => 'EUR'], ['label' => 'USD', 'value' => 'USD']]" wire:model="currency_type" />
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-1/2">
            <x-input label="Sigla" placeholder="Ej. BNB" wire:model="initials" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-color wire:model="color" label="Color" />
        </div>
    </div>
    <x-select.styled label="Departamento" wire:model="department" :options="[
        ['label' => 'Ninguno', 'value' => 'none'],
        ['label' => 'Recursos Humanos', 'value' => 'Human Resources'],
        ['label' => 'Inventario', 'value' => 'Inventory'],
    ]" />
</x-crud.modal>
