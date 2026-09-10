<div class="space-y-4">
    <x-crud.header title="Departamentos" create-label="Agregar Departamento" create-permission="Crear departamentos" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="departaments">
            @interact('column_action', $row)
                <div class="flex gap-1">
                    @can('Editar departamentos')
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::departament', { 'departament' : '{{ $row->id }}'})" />
                    @endcan
                    @can('Eliminar departamentos')
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                    @endcan
                </div>
            @endinteract
        </x-table>
    </div>

    @include('livewire.departaments.form')
</div>
