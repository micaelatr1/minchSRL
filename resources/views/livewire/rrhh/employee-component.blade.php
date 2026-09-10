<div>
    <x-crud.header title="Empleados" create-label="Agregar Empleado" create-permission="Crear empleados" />

    <x-table :$headers :$rows filter paginate loading id="employees">
        @interact('column_status', $row)
            <x-badge :label="$row->status === 'active' ? 'Activo' : 'Inactivo'"
                :color="$row->status === 'active' ? 'success' : 'danger'" />
        @endinteract

        @interact('column_base_salary', $row)
            <span class="font-mono">Bs. {{ number_format($row->base_salary, 2, ',', '.') }}</span>
        @endinteract

        @interact('column_action', $row)
            <div class="flex gap-1">
                @can('Editar empleados')
                <x-button.circle icon="pencil" color="blue" light
                    wire:click="$dispatch('load::employee', { employee: {{ $row->id }} })" />
                @endcan
                @can('Eliminar empleados')
                <x-button.circle icon="trash" color="red" light
                    onclick="confirmDelete('{{ $row->id }}')" />
                @endcan
            </div>
        @endinteract
    </x-table>

    @include('livewire.rrhh.employee-form')
</div>

