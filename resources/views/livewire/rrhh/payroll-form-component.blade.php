<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('rrhh.payrolls') }}" wire:navigate
            class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
            <x-label class="text-dark-400 text-xs uppercase tracking-wider">Período</x-label>
            <p class="text-2xl font-bold text-white mt-1">{{ $period }}</p>
        </div>
        <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
            <x-label class="text-dark-400 text-xs uppercase tracking-wider">Total Pagado</x-label>
            <p class="text-2xl font-bold text-emerald-400 mt-1">Bs. {{ number_format($payroll->totalPaid(), 2, ',', '.') }}</p>
        </div>
        <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
            <x-label class="text-dark-400 text-xs uppercase tracking-wider">Estado</x-label>
            <div class="mt-1">
                <x-badge :label="$payroll->status === 'paid' ? 'Pagado' : 'Pendiente'"
                    :color="$payroll->status === 'paid' ? 'success' : 'warning'" />
            </div>
        </div>
    </div>

    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-base font-semibold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                Empleados Pagados
                <span class="text-xs text-dark-400 font-normal">({{ count($paidEmployees) }})</span>
            </h3>
            <div class="w-60">
                <x-input wire:model.live.debounce.300ms="searchPaid" placeholder="Buscar por nombre o CI..." icon="magnifying-glass" size="sm" />
            </div>
        </div>
        <div class="overflow-x-auto soft-scrollbar">
            <table class="w-full divide-y divide-dark-600/20 text-sm">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Empleado</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-dark-300 text-[11px]">CI</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Sueldo Base</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Bonos</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Descuentos</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Neto</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($paidEmployees as $item)
                        <tr class="hover:bg-primary-500/5 transition-colors duration-150">
                            <td class="px-4 py-3 text-dark-200 font-medium">{{ $item['employee_name'] }}</td>
                            <td class="px-4 py-3 text-dark-400">{{ $item['ci'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-dark-200">Bs. {{ number_format($item['base_salary'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-400">Bs. {{ number_format($item['bonuses'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-red-400">Bs. {{ number_format($item['discounts'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-white">Bs. {{ number_format($item['net_salary'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-1">
                                    @can('Editar planillas')
                                    <x-button.circle icon="pencil" color="blue" light size="xs"
                                        wire:click="editPayment({{ $item['id'] }})" />
                                    @endcan
                                    @can('Eliminar planillas')
                                    <x-button.circle icon="trash" color="red" light size="xs"
                                        onclick="confirmDelete({{ $item['id'] }})" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-dark-400 italic">
                                No hay empleados pagados en esta planilla.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($paidEmployees) > 0)
                <tfoot class="bg-dark-700/30">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-sm font-semibold text-dark-200">Totales</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-dark-200">Bs. {{ number_format(collect($paidEmployees)->sum('base_salary'), 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-400">Bs. {{ number_format(collect($paidEmployees)->sum('bonuses'), 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-red-400">Bs. {{ number_format(collect($paidEmployees)->sum('discounts'), 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-white">Bs. {{ number_format(collect($paidEmployees)->sum('net_salary'), 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    @if ($payroll->status !== 'paid')
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-base font-semibold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                Empleados Pendientes
                <span class="text-xs text-dark-400 font-normal">({{ count($pendingEmployees) }})</span>
            </h3>
            <div class="w-60">
                <x-input wire:model.live.debounce.300ms="searchPending" placeholder="Buscar por nombre o CI..." icon="magnifying-glass" size="sm" />
            </div>
        </div>
        <div class="overflow-x-auto soft-scrollbar">
            <table class="w-full divide-y divide-dark-600/20 text-sm">
                <thead>
                    <tr class="bg-dark-700/50">
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Empleado</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider text-dark-300 text-[11px]">CI</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Antigüedad</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Sueldo Base</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider text-dark-300 text-[11px]">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-600/10">
                    @forelse ($pendingEmployees as $item)
                        <tr class="hover:bg-primary-500/5 transition-colors duration-150">
                            <td class="px-4 py-3 text-dark-200 font-medium">{{ $item['employee_name'] }}</td>
                            <td class="px-4 py-3 text-dark-400">{{ $item['ci'] }}</td>
                            <td class="px-4 py-3 text-center text-dark-400">{{ $item['tenure'] }}</td>
                            <td class="px-4 py-3 text-right font-mono text-dark-200">Bs. {{ number_format($item['base_salary'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-button.circle icon="currency-dollar" color="emerald" light
                                    wire:click="openPaymentModal({{ $item['id'] }})" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-dark-400 italic">
                                Todos los empleados activos han sido pagados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <x-crud.modal entity="Pago" :edit="$editingDetailId" saveMethod="confirmPayment" modalId="payment-modal" offcanvas>
        <div x-data="{
            name: '', ci: '', tenure: '', baseSalary: 0, bonuses: 0, discounts: 0,
            get net() {
                return (Number(this.baseSalary) || 0) + (Number(this.bonuses) || 0) - (Number(this.discounts) || 0);
            },
            fmt(v) {
                return Number(v).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }" @open-payment-modal-window.window="
            name = $wire.selectedEmployeeName;
            ci = $wire.selectedEmployeeCi;
            tenure = $wire.selectedEmployeeTenure;
            baseSalary = Number($wire.selectedEmployeeBaseSalary) || 0;
            bonuses = Number($wire.modalBonuses) || 0;
            discounts = Number($wire.modalDiscounts) || 0;
            $nextTick(() => { window.dispatchEvent(new CustomEvent('modal:payment-modal-open')); });
        ">

            <div class="grid grid-cols-2 gap-4 p-4 bg-dark-700/40 rounded-lg border border-dark-600/15 mb-5">
                <div>
                    <x-label class="text-dark-400">Empleado</x-label>
                    <p class="text-sm font-medium text-white mt-0.5" x-text="name"></p>
                </div>
                <div>
                    <x-label class="text-dark-400">CI</x-label>
                    <p class="text-sm text-dark-200 mt-0.5" x-text="ci"></p>
                </div>
                <div>
                    <x-label class="text-dark-400">Antigüedad</x-label>
                    <p class="text-sm text-dark-200 mt-0.5" x-text="tenure"></p>
                </div>
                <div>
                    <x-label class="text-dark-400">Sueldo Base</x-label>
                    <p class="text-sm font-mono text-white mt-0.5" x-text="'Bs. ' + fmt(baseSalary)"></p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <x-input x-model="bonuses" type="number" step="0.01" min="0"
                        @input.debounce.200ms="$wire.set('modalBonuses', bonuses)"
                        label="Bonos (Bs.)" />
                </div>
                <div>
                    <x-input x-model="discounts" type="number" step="0.01" min="0"
                        @input.debounce.200ms="$wire.set('modalDiscounts', discounts)"
                        label="Descuentos (Bs.)" />
                </div>
                <div class="p-4 bg-dark-700/40 rounded-lg border border-dark-600/15">
                    <div class="flex items-center justify-between">
                        <x-label class="text-dark-400">Sueldo Neto</x-label>
                        <span class="text-xl font-bold font-mono text-emerald-400" x-text="'Bs. ' + fmt(net)"></span>
                    </div>
                    <div class="mt-2 text-[11px] text-dark-500 font-mono space-y-0.5">
                        <div class="flex justify-between">
                            <span>Base</span>
                            <span x-text="'Bs. ' + fmt(baseSalary)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>+ Bonos</span>
                            <span class="text-emerald-500" x-text="'+ Bs. ' + fmt(Number(bonuses) || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>- Descuentos</span>
                            <span class="text-red-400" x-text="'- Bs. ' + fmt(Number(discounts) || 0)"></span>
                        </div>
                        <div class="border-t border-dark-600/20 pt-0.5 flex justify-between font-semibold text-dark-300">
                            <span>= Neto</span>
                            <span x-text="'Bs. ' + fmt(net)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-crud.modal>
</div>
