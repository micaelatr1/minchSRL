<div x-on:open-edit-modal.window="$tsui.open.modal('crud-modal')">
    <x-crud.header title="Planilla de Sueldos" create-label="Nueva Planilla" create-permission="Crear planillas" />

    <div class="flex flex-wrap items-center gap-3 mb-3">
        <div class="w-40">
            <x-date month-year-only wire:model.live="filterPeriodFrom" placeholder="Desde" />
        </div>
        <div class="w-40">
            <x-date month-year-only wire:model.live="filterPeriodTo" placeholder="Hasta" />
        </div>
        <x-button color="secondary" light wire:click="clearFilters" icon="x-mark" size="sm">
            Limpiar
        </x-button>
    </div>

    <x-table :$headers :$rows paginate loading id="payrolls">

        @interact('column_total_paid', $row)
            <span class="font-mono font-semibold">Bs. {{ number_format($row->total_paid, 2, ',', '.') }}</span>
        @endinteract

        @interact('column_status', $row)
            @php
                $map = ['pending' => ['Pendiente', 'yellow'], 'paid' => ['Pagado', 'green']];
                [$label, $color] = $map[$row->status] ?? ['Desconocido', 'gray'];
            @endphp
            <span style="background:{{ $color === 'green' ? '#059669' : '#ca8a04' }}"
                  class="inline-block rounded-full px-3 py-0.5 text-xs font-semibold text-white">
                {{ $label }}
            </span>
        @endinteract

        @interact('column_action', $row)
            <div class="flex gap-1">
                @can('Editar planillas')
                <x-button.circle icon="pencil" color="blue" light
                    wire:click="edit({{ $row->id }})" />
                @endcan
                @can('Crear planillas')
                <x-button.circle icon="currency-dollar" color="emerald" light
                    :href="route('rrhh.payrolls.form', $row->id)" wire:navigate />
                @endcan
                @can('Eliminar planillas')
                <x-button.circle icon="trash" color="red" light
                    onclick="confirmDelete('{{ $row->id }}')" />
                @endcan
            </div>
        @endinteract
    </x-table>

    <x-crud.modal entity="Planilla" :edit="$editingPayrollId" storeMethod="save" updateMethod="update" modalId="crud-modal">
        <div class="space-y-4">
            <div>
                <x-label>Período</x-label>
                <x-date month-year-only wire:model="period" />
                @error('period') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-label>Estado</x-label>
                <x-select.styled wire:model="status" :options="[['label' => 'Pendiente', 'value' => 'pending'], ['label' => 'Pagado', 'value' => 'paid']]" />
                @error('status') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
            </div>
        </div>
    </x-crud.modal>
</div>


