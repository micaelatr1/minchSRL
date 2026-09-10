<x-crud.modal entity="Impuesto" :edit="$this->id">
    <x-input label="Nombre de impuesto" placeholder="Nombre" wire:model="name" />
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-1/3">
            <x-input label="Iniciales" placeholder="Ej. IVA" wire:model="initials" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-input label="Numero" placeholder="Ej. 404" wire:model="number" type="number" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-input label="Porcentaje %" placeholder="Ej. 10.50" type="number" wire:model="applied_discount" />
        </div>
    </div>
    <x-select.styled label="Seleccione una opcion" :options="[['label' => 'Todos', 'value' => 'A'], ['label' => 'Bienes', 'value' => 'G'], ['label' => 'Servicios', 'value' => 'S']]" wire:model="type" />
</x-crud.modal>
