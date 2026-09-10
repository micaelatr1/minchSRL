<x-crud.modal entity="Empleado" :edit="$this->employeeId" store-method="store" update-method="update">
    <x-input label="Nombre Completo" placeholder="Nombre completo" wire:model="full_name" />
    <x-input label="Cédula de Identidad" placeholder="C.I." wire:model="ci" />
    <x-select.styled label="Departamento" wire:model="department_id" required
        :options="\App\Models\Departament::all()->map(fn($d) => ['label' => $d->area, 'value' => $d->id])->toArray()" />
    <x-input label="Código de Empleado" placeholder="EMP-001" wire:model="code" />
    <x-input label="Cargo" placeholder="Cargo del empleado" wire:model="position" />
    <x-input label="Salario Base (Bs)" type="number" step="0.01" wire:model="base_salary" />
    <x-input label="Fecha de Ingreso" type="date" wire:model="hire_date" />
    <x-select.styled label="Estado" wire:model="status" :options="[
        ['label' => 'Activo', 'value' => 'active'],
        ['label' => 'Inactivo', 'value' => 'inactive'],
    ]" />
</x-crud.modal>
