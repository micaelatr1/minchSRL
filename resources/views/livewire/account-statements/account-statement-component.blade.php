<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-1">
            <button type="button"
                wire:click="$set('activeTab', 'supplier')"
                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $this->activeTab === 'supplier' ? 'bg-primary-500 text-white' : 'text-gray-600 dark:text-gray-300' }}">
                Proveedores
            </button>
            <button type="button"
                wire:click="$set('activeTab', 'customer')"
                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $this->activeTab === 'customer' ? 'bg-primary-500 text-white' : 'text-gray-600 dark:text-gray-300' }}">
                Clientes
            </button>
        </div>
    </div>

    <div>
        <x-table :$headers :$rows filter paginate loading id="account-statements">
            @interact('column_balance', $row)
                @php
                    $balance = (float) $row->balance;
                @endphp
                <x-badge :color="$balance < 0 ? 'red' : 'emerald'" size="sm">
                    {{ number_format($balance, 2, ',', '.') }}
                </x-badge>
            @endinteract
            @interact('column_action', $row)
                <div class="flex gap-1 flex-row justify-center">
                    @can('Ver estados de cuenta')
                        <x-button.circle icon="eye" color="violet" light
                            href="{{ route('accounts.statement.view', ['type' => $this->activeTab, 'id' => $row->id]) }}"
                            wire:navigate />
                    @endcan
                </div>
            @endinteract
        </x-table>
    </div>
</div>
