<div class="space-y-4">
    <x-crud.header title="Proveedores" create-label="Agregar Proveedor" create-permission="Crear proveedores" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="suppliers">
            @interact('column_action', $row)
                <div class="flex gap-1">
                    @can('Editar proveedores')
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::supplier', { 'supplier' : '{{ $row->id }}'})" />
                    @endcan
                    @can('Eliminar proveedores')
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                    @endcan
                </div>
            @endinteract
        </x-table>
    </div>

    @include('livewire.suppliers.form')
</div>
