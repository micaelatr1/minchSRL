<div class="space-y-4">
    <x-crud.header title="Roles" create-label="Agregar Rol" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="roles">
            @interact('column_action', $row)
                <div class="flex gap-1 flex-row justify-center">
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::role', { 'role' : '{{ $row->id }}'})" />
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                </div>
            @endinteract
        </x-table>
    </div>
    @include('livewire.roles.form')
</div>
