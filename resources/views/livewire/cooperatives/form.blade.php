<x-crud.modal entity="Cooperativa" :edit="$this->id">
    <x-input label="Nombre" placeholder="Nombre" wire:model="name" />
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-1/2">
            <x-input label="Ingrese el numero de NIT" type="number" placeholder="NIT" wire:model="NIT" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-input label="Ingrese el numero de NIM" type="text" placeholder="NIM" wire:model="NIM" />
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-1/2">
            <x-input label="Ingrese la concesión" placeholder="Concesión" wire:model="concession" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-input label="Ingrese la vocamina" placeholder="Vocamina" wire:model="mine" />
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="w-full sm:w-1/3">
            <x-input label="Ingrese el municipio" placeholder="Municipio" wire:model="municipality" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-input label="Ingrese el Aporte a Coop." placeholder="Aporte a Coop." wire:model="contribution" />
        </div>
        <div class="w-full sm:w-1/3">
            <x-input label="Ingrese el Aporte a Comibol." placeholder="Aporte a Comibol." wire:model="comibol" />
        </div>
    </div>
</x-crud.modal>
