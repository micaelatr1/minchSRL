<x-crud.modal entity="Proveedor" :edit="$this->supplierId" store-method="store" update-method="update">
    <x-input label="Nombre Completo" placeholder="Nombre completo" wire:model="full_name" />
    <x-input label="Cédula de Identidad" placeholder="C.I." wire:model="ci" />
    <x-input label="Descripción" placeholder="Descripción del proveedor" wire:model="description" />
</x-crud.modal>
