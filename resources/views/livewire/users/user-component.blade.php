<div class="space-y-4">
    @can('Crear usuarios')
        <x-crud.header title="Usuarios" create-label="Agregar Usuario" />
    @else
        <x-crud.header title="Usuarios" />
    @endcan

    <div>
        <x-table :$headers :$rows filter paginate loading id="users">
            @interact('column_roles', $row)
                <div class="flex flex-wrap gap-1">
                    @foreach ($row->roles as $role)
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-primary-600/10 text-primary-600 dark:text-primary-400 border border-primary-500/20">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            @endinteract
            @interact('column_action', $row)
                <div class="flex gap-1 flex-row justify-center">
                    @can('Editar usuarios')
                        <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::user', { 'user' : '{{ $row->id }}'})" />
                    @endcan
                    @can('Eliminar usuarios')
                        <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                    @endcan
                </div>
            @endinteract
        </x-table>
    </div>
    @include('livewire.users.form')
</div>
