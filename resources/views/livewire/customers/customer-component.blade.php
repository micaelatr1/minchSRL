<div class="space-y-4">
    <x-crud.header title="Clientes" create-label="Agregar Cliente" create-permission="Crear clientes" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="customers">
            @interact('column_tipo', $row)
                @if ($row->tipo)
                    <span @class([
                        'font-semibold',
                        'text-red-500 dark:text-red-400' => $row->tipo_color === 'red',
                        'text-green-600 dark:text-green-400' => $row->tipo_color === 'green',
                    ])>{{ $row->tipo }}</span>
                @endif
            @endinteract
            @interact('column_file', $row)
                @if ($row->file_path)
                    <x-button.circle icon="document-arrow-down" color="primary" light :href="$row->file_path" target="_blank" title="Ver archivo" />
                @else
                    <span class="text-gray-400 dark:text-dark-500">—</span>
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
