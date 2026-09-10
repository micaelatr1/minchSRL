<div >
    <x-card>
        <x-slot:header>
            <span class="font-semibold text-base">{{$this->id?'Actualizar':'Agregar'}} | Recibo</span>
        </x-slot:header>
        <div class="row ">
            <form id="user-create" wire:submit="save" class="space-y-2">
                <div class="space-y-1">
                    <livewire:supplier-search  :ci="$ci"  />
                    <x-input label="Nombre y Apellido" wire:model="full_name" placeholder="Nombre completo" />

                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="w-full sm:w-1/2">
                            <x-select.styled wire:model.live="type" label="Tipo de recibo" :options="[
                                ['label' => 'Bienes', 'value' => 'G'],
                                ['label' => 'Servicios', 'value' => 'S'],
                            ]" />
                        </div>
                        <div class="w-full sm:w-1/2">
                            <x-date label="Seleccione una fecha" wire:model="date" />
                            @error('date') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <x-textarea wire:model="summary" label="Resumen" rows="3" />
                    </div>
                    <div class="col-12">
                        <x-textarea wire:model="description" label="Descripción" rows="5" />
                    </div>
                </div>

                {{-- Retenciones --}}
                <x-card class="mt-4">
                    <x-slot:header>
                        <span class="font-semibold text-base">RETENCIONES IMPOSITIVAS</span>
                    </x-slot:header>
                <div
    x-data="{
        amount: @entangle('amount'),
        taxes: @js($this->taxes),
        descounts: [],

        get sumTaxes() {
            return this.taxes.reduce((sum, t) => sum + parseFloat(t.applied_discount || 0), 0);
        },
        get totalBruto() {
            let sum = this.sumTaxes;
            if (sum >= 100) return 0;
            return parseFloat(this.amount || 0) / (100 - sum) * 100;
        },
        get totalRetenciones() {
            return this.descounts.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
        },
        calcularDescounts() {
            this.descounts = this.taxes.map(t => this.totalBruto * (t.applied_discount / 100));
        },
        init() {
            this.descounts = this.taxes.map(() => 0);
            this.$watch('amount', () => this.calcularDescounts());
            this.calcularDescounts();
        }
    }"
    wire:key="retenciones-{{ $this->type }}"
>
                        {{-- Precio pactado --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <x-input label="Precio Pactado" type="number" step="any" x-model="amount"  wire:model="amount"/>
                            </div>
                        </div>

                        {{-- Lista de impuestos --}}
                        <div class="row g-3 mb-4">
                            <template x-for="(taxe, index) in taxes" :key="index">
                                <div class="col-12">
                                    <x-card>
            <form id="user-create" wire:submit="save" class="space-y-2" x-on:keydown.cmd.enter="$wire.{{ $this->id ? 'update' : 'store' }}()">

                                            <div class="flex flex-col sm:flex-row gap-4">
                                                <div class="w-full sm:w-1/2">
                                                    <span class="fw-semibold">
                                                        Impuesto Form. <span x-text="taxe.number"></span>
                                                    </span>
                                                    <x-badge color="gray">
                                                        <span x-text="taxe.applied_discount + '%'"></span>
                                                    </x-badge>
                                                </div>
                                                <div class="w-full sm:w-1/2">
                                                    <span class="text-secondary text-sm">Monto retenido:</span>
                                                    <span class="fw-semibold"
                                                        x-text="'Bs. ' + (descounts[index] !== undefined ? descounts[index].toFixed(2) : '0.00')"></span>
                                                </div>
                                            </div>
                                        </form>
                                    </x-card>
                                </div>
                            </template>
                        </div>
                        {{-- Totales --}}
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <x-input label="Total Retenciones" type="number"
                                    x-bind:value="totalRetenciones.toFixed(2)" readonly />
                            </div>
                            <div class="col-md-4 mb-3">
                                <x-input label="Total Bruto" type="number" x-bind:value="totalBruto.toFixed(2)"
                                    readonly />
                            </div>
                            <div class="col-md-4 mb-3">
                                <x-input label="Liquido a Pagar" type="number"
                                    x-bind:value="parseFloat(amount || 0).toFixed(2)" readonly />
                            </div>
                        </div>
                    </div>
                </x-card>

                {{-- Acciones --}}
                <x-slot:footer>
                    <div class="flex justify-end gap-2">
                        <x-button wire:navigate href="{{ route('retentions') }}" text="Cancelar" icon="x-mark"
                            color="secondary" />
                        @if ($this->id)
                            <x-button wire:click.prevent="update()" wire:loading.attr="disabled" wire:target="update"
                                icon="clipboard-document" color="fuchsia" outline>
                                <span wire:loading.remove wire:target="update">Actualizar</span>
                                <span wire:loading wire:target="update" class="flex items-center gap-2">
                                    <x-ui.loading-spinner class="h-4 w-4" /> Guardando...
                                </span>
                            </x-button>
                        @else
                            <x-button wire:click.prevent="store()" wire:loading.attr="disabled" wire:target="store"
                                icon="archive-box-arrow-down" color="fuchsia" outline>
                                <span wire:loading.remove wire:target="store">Guardar</span>
                                <span wire:loading wire:target="store" class="flex items-center gap-2">
                                    <x-ui.loading-spinner class="h-4 w-4" /> Guardando...
                                </span>
                            </x-button>
                        @endif
                    </div>
                </x-slot:footer>
            </form>
        </div>
    </x-card>
</div>
