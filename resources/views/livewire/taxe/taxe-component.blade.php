<div class="space-y-4">
    <x-crud.header title="Impuestos" create-label="Agregar Impuesto" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="taxes">
            @interact('column_action', $row)
                <div class="flex gap-1">
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::taxe', { 'taxe' : '{{ $row->id }}'})" />
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                </div>
            @endinteract
        </x-table>
    </div>
    @include('livewire.taxe.form')
</div>
