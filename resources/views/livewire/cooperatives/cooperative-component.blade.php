<div class="space-y-4">
    <x-crud.header title="Cooperativas" create-label="Agregar Cooperativa" create-permission="Crear cooperativas" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="suppliers">
            @interact('column_action', $row)
                <div class="flex gap-1">
                    @can('Editar cooperativas')
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::cooperative', { 'cooperative' : '{{ $row->id }}'})" />
                    @endcan
                    @can('Eliminar cooperativas')
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                    @endcan
                </div>
            @endinteract
        </x-table>
    </div>
    @include('livewire.cooperatives.form')
</div>
