<div>
    <x-card>
        <x-slot:header>
            <span class="font-semibold text-base">{{ $this->id ? 'Actualizar' : 'Nuevo' }} | Contrato</span>
        </x-slot:header>

        <form id="contract-form" wire:submit="{{ $this->id ? 'update' : 'store' }}" class="space-y-2">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="w-full sm:w-1/2">
                    <x-select.styled wire:model.live="type" label="Tipo" :options="[
                        ['label' => 'Proveedor', 'value' => 'supplier'],
                        ['label' => 'Cliente', 'value' => 'customer'],
                    ]" />
                </div>
                <div class="w-full sm:w-1/2">
                    <x-date label="Fecha de inicio" wire:model="start_date" />
                    @error('start_date') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <livewire:supplier-search :ci="$ci" />

            <div class="flex flex-col sm:flex-row gap-2">
                <div class="w-full sm:w-1/2">
                    <x-input label="Nombre y Apellido" wire:model="full_name" placeholder="Nombre completo" />
                    @error('full_name') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="w-full sm:w-1/2">
                    <x-input label="Teléfono" wire:model="phone" placeholder="7654321" />
                    @error('phone') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <x-textarea wire:model="description" label="Descripción" rows="3" />
            @error('description') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror

            <div class="flex flex-col sm:flex-row gap-2">
                <div class="w-full sm:w-1/2">
                    <x-input label="Monto total (Bs)" type="number" step="0.01" wire:model="total_amount" placeholder="0.00" />
                    @error('total_amount') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="w-full sm:w-1/2">
                    <x-date label="Fecha de finalización (opcional)" wire:model="end_date" />
                    @error('end_date') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <div class="w-full sm:w-1/2">
                    @if ($existingFile)
                        <div class="mb-2">
                            <span class="text-sm text-dark-400">Archivo actual:</span>
                            <a href="{{ asset('storage/' . $existingFile) }}" target="_blank"
                               class="text-sm text-blue-400 hover:text-blue-300 ml-1">Ver PDF</a>
                        </div>
                    @endif
                    <x-input label="Archivo (PDF, máx 2MB)" type="file" wire:model="file" accept="application/pdf" />
                    @error('file') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2">
                    <x-button wire:navigate href="{{ route('contracts') }}" text="Cancelar" icon="x-mark" color="secondary" />

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
    </x-card>
</div>
