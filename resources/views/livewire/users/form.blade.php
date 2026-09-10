<x-crud.modal entity="Usuario" :edit="$this->id">
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="w-full sm:w-1/2">
            <x-input label="Nombre" placeholder="Nombre" wire:model="name" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-input label="Apellido" placeholder="Apellido" wire:model="last_name" />
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="w-full sm:w-1/2">
            <x-input label="Cedula de Identidad" placeholder="Ej. 123" wire:model="ci" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-date label="Fecha de Nacimiento" wire:model="birthdate" />
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="w-full sm:w-1/3">
            <x-select.styled label="Seleccione una opcion" :options="[['label' => 'Masculino', 'value' => 'M'], ['label' => 'Femenino', 'value' => 'F']]" wire:model="gender" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-input label="Telefono" placeholder="Ej. 77731123" wire:model="phone" type="number" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-select.styled label="Seleccione el rol" :options="$options" wire:model="rols" multiple />
        </div>
    </div>
    <x-input label="Correo electronico" placeholder="Email" wire:model="email" type="email" />
</x-crud.modal>
