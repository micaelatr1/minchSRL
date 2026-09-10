<div class="space-y-4">
    <x-crud.header title="Cuentas" create-label="Agregar Cuenta" />

    <div>
        <x-table :$headers :$rows filter paginate loading id="accounts">
            @interact('column_action1', $row)
                <div class="flex gap-1">
                    <span class="rounded-md outline-hidden inline-flex items-center border px-2 py-0.5 font-bold text-xs border-neutral-300 bg-neutral-300 dark:bg-neutral-700/30 dark:border-transparent text-neutral-600 dark:text-neutral-400" style="background: {{ $row->color }}; color: white;">
                        {{ $row->initials }}{{ $row->color }}
                    </span>
                </div>
            @endinteract
            @interact('column_action', $row)
                <div class="flex gap-1">
                    <x-button.circle icon="pencil" color="blue" light wire:click="$dispatch('load::account', { 'account' : '{{ $row->id }}'})" />
                    <x-button.circle icon="trash" color="red" light onclick="confirmDelete('{{ $row->id }}')" />
                </div>
            @endinteract
        </x-table>
    </div>
    @include('livewire.accounts.form')
</div>
