<div class="space-y-6">
    <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.purchases') }}" wire:navigate
               class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $id ? 'Editar' : 'Nueva' }} Compra</h2>
        </div>
    </div>

    <form @submit.prevent="syncItems().then(() => $wire.save())"
          x-data="{
             items: {{ Js::from($items) }},
             addItem() { this.items.push({ _key: 'new_'+Date.now(), product_id: '', product_name: '', quantity: '1', unit_cost: '0' }); },
             removeItem(index) { this.items.splice(index, 1); },
             syncItems() { return $wire.set('items', JSON.parse(JSON.stringify(this.items))) },
             total() { return this.items.reduce((s, i) => s + (parseFloat(i.quantity ?? 0) * parseFloat(i.unit_cost ?? 0)), 0); }
          }">
        <div class="space-y-6">
            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-dark-300">Datos de la Compra</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-date label="Fecha" wire:model="date" />
                        @error('date') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input label="Nro. Factura" wire:model="invoice_number" placeholder="Ej: 12345" />
                        @error('invoice_number') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <x-label>Proveedor</x-label>
                    <livewire:supplier-search :ci="$ci" wire:key="supplier-search-{{ $id ?? 'new' }}" />
                    @error('supplier_id') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input label="Nombre y Apellido" wire:model="supplier_name" placeholder="Nombre completo" />
                    <x-input label="Teléfono" wire:model="supplier_phone" placeholder="Teléfono" />
                </div>
            </div>

            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-dark-300">Registro Bancario</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <x-select.styled label="Tipo de Pago" wire:model="payment_type" :options="[
                            ['label' => 'Transferencia', 'value' => 'T'],
                            ['label' => 'Cheque', 'value' => 'CH'],
                        ]" />
                        @error('payment_type') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-input label="Nro. Cheque/Transacción" wire:model="number_check" placeholder="Opcional" />
                    </div>

                </div>
                <div>
                    <x-textarea label="Descripción" wire:model="description" placeholder="Descripción del movimiento (opcional)" />
                </div>
            </div>

            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-dark-300">Productos</h3>
                    <x-button color="primary" size="xs" icon="plus" @click="addItem" type="button">Agregar Producto</x-button>
                </div>

                @error('items') <span class="text-sm text-red-400">{{ $message }}</span> @enderror


                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="item._key">
                        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-end gap-2 sm:gap-3 p-3 rounded-lg bg-dark-700/30 border border-dark-600/20"
                             x-init="$nextTick(() => { let s = $el.querySelector('[x-data]')?.__x?.$data; if (s && !s.options.length) { s.sync(); if (item.product_id) s.hydrate(); } })">
                            <div class="col-span-2 sm:flex-1 sm:min-w-[200px]">
                                <x-select.styled label="Producto" x-model="item.product_id" required searchable
                                    :options="$products->map(fn($p) => ['label' => $p->name . ' (Stock: ' . number_format($p->stock, 2, ',', '.') . ' ' . $p->unit_label . ')', 'value' => (string) $p->id])->toArray()"
                                    select="label:label|value:value" />
                                <span class="text-sm text-red-400" x-show="$wire.$errors?.['items.'+index+'.product_id']" x-text="$wire.$errors?.['items.'+index+'.product_id']?.[0] ?? ''"></span>
                            </div>
                            <div class="sm:w-28">
                                <x-input x-model="item.quantity" label="Cantidad" type="number" step="0.0001" required />
                                <span class="text-sm text-red-400" x-show="$wire.$errors?.['items.'+index+'.quantity']" x-text="$wire.$errors?.['items.'+index+'.quantity']?.[0] ?? ''"></span>
                            </div>
                            <div class="sm:w-32">
                                <x-input x-model="item.unit_cost" label="Costo Unit. (Bs)" type="number" step="0.0001" required />
                                <span class="text-sm text-red-400" x-show="$wire.$errors?.['items.'+index+'.unit_cost']" x-text="$wire.$errors?.['items.'+index+'.unit_cost']?.[0] ?? ''"></span>
                            </div>
                            <div class="col-span-2 sm:w-auto flex items-center justify-between sm:justify-start gap-2 pb-1">
                                <span class="text-sm text-dark-300 whitespace-nowrap">
                                    Subtotal: <strong class="text-primary-300 font-mono" x-text="(parseFloat(item.quantity ?? 0) * parseFloat(item.unit_cost ?? 0)).toLocaleString('de-DE', { minimumFractionDigits: 2 })"></strong>
                                </span>
                                <button type="button" @click="removeItem(index)"
                                        class="p-1.5 rounded-lg text-red-400 hover:bg-red-600/10 transition-all duration-200"
                                        title="Eliminar producto">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div class="text-center py-8" x-show="items.length === 0">
                        <p class="text-dark-400">Agrega al menos un producto a la compra.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-dark-600/20" x-show="items.length > 0">
                    <div class="text-right">
                        <p class="text-sm text-dark-400">Total Compra</p>
                        <p class="text-2xl font-bold text-primary-300 font-mono" x-text="total().toLocaleString('de-DE', { minimumFractionDigits: 2 }) + ' Bs'"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <x-button color="secondary" :href="route('inventory.purchases')" wire:navigate>Cancelar</x-button>
                <x-button type="submit" color="primary" loading="save">
                    {{ $id ? 'Actualizar' : 'Registrar' }} Compra
                </x-button>
            </div>
        </div>
    </form>
</div>
