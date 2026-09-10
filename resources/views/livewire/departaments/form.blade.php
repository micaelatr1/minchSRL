<x-crud.modal entity="Departamento" :edit="$this->departamentId">
    <x-input label="Área" placeholder="Nombre del área" wire:model="area" />
    <x-input label="Descripción" placeholder="Descripción del departamento" wire:model="description" />
    <x-select.styled label="Cuenta" wire:model="account_id" :options="\App\Models\Account::select('id as value', 'name as label')->get()->toArray()" />
    <x-select.styled label="Usuario" wire:model="user_id" :options="\App\Models\User::select('id as value', 'name as label')->get()->toArray()" />
</x-crud.modal>
