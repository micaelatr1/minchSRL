<div class="space-y-4">
    <x-crud.header title="Clientes" create-label="Agregar Cliente" create-permission="Crear clientes" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="customers">
            @interact('column_file', $row)
                @if ($row->file_path)
                    <a href="{{ $row->file_path }}" target="_blank" class="text-primary-600 hover:text-primary-800 underline text-sm">{{ $row->file }}</a>
                @endif
            @endinteract
            @interact('column_action', $row)
                <div class="flex gap-1">
                    @can('Editar clientes')
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::customer', { 'customer' : '{{ $row->id }}'})" />
                    @endcan
                    @can('Eliminar clientes')
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                    @endcan
                </div>
            @endinteract
        </x-table>
    </div>

    @include('livewire.customers.form')
</div>
