<div>
    <x-crud.header title="Vacaciones" />

    <div class="flex flex-col sm:flex-row gap-2 pt-4">
        <div class="w-full sm:w-2/3">
            <x-card title="Vacaciones Tomadas">
                <x-table :$headers :$rows paginate loading id="vacations-taken">
                    @interact('column_action', $row)
                        <x-button.circle icon="x-mark" color="red" light
                            onclick="confirmRevoke({{ $row->id }})" />
                    @endinteract
                </x-table>
            </x-card>
        </div>

        <div class="w-full sm:w-1/3">
            <x-card title="Vacaciones Pendientes">
                <div class="mb-3">
                    <x-input placeholder="Buscar por nombre o CI..." wire:model.live="search" />
                </div>

                @forelse ($pendingEmployees as $employee)
                    <div class="mb-2 rounded-lg border border-gray-200 dark:border-dark-600 bg-white dark:bg-dark-700 p-3 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm">{{ $employee->full_name }}</p>
                                <p class="text-xs text-gray-500">CI: {{ $employee->ci }}</p>
                                <p class="text-xs text-gray-500">Antigüedad: {{ $employee->tenure }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-primary-600">{{ $employee->vacation_days }}</p>
                                <p class="text-xs text-gray-500">días</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <x-button icon="check" color="green" class="w-full" size="sm"
                                onclick="confirmValidate({{ $employee->id }})" />
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No hay empleados con vacaciones pendientes.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</div>

<script>
    function confirmValidate(id) {
        const component = Livewire.getByName('rrhh.vacation-component')[0];

        $tsui.interaction('dialog')
            .wireable(component.id)
            .question('Advertencia', '¿Estás seguro de eliminar este registro?')
            .confirm('Confirmar', 'validateVacation', [id])
            .cancel('Cancelar')
            .send();
    }

    function confirmRevoke(id) {
        const component = Livewire.getByName('rrhh.vacation-component')[0];

        $tsui.interaction('dialog')
            .wireable(component.id)
            .question('Advertencia', '¿Estás seguro de eliminar este registro?')
            .confirm('Confirmar', 'delete', [id])
            .cancel('Cancelar')
            .send();
    }
</script>

