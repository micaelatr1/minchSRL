<div class="space-y-4">
    <x-crud.header title="Productos" create-label="Agregar Producto" :create-route="route('inventory.products.form')" />

    <div class="flex flex-col sm:flex-row gap-4 sm:items-end sm:justify-between bg-dark-800/40 backdrop-blur-sm rounded-xl p-4 border border-dark-600/20">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="w-64">
                <x-input wire:model.live.debounce.300ms="search" placeholder="Buscar producto..." icon="magnifying-glass" />
            </div>
            <div class="w-48">
                <x-select.styled wire:model.live="categoryFilter" placeholder="Todas las categorías" :options="[
                    ['label' => 'Todos', 'value' => ''],
                    ['label' => 'Materia Prima', 'value' => 'materia_prima'],
                    ['label' => 'Insumo', 'value' => 'insumo'],
                    ['label' => 'Repuesto', 'value' => 'repuesto'],
                    ['label' => 'Combustible', 'value' => 'combustible'],
                    ['label' => 'Otro', 'value' => 'otro'],
                ]" />
            </div>
        </div>
    </div>

    <div class="relative">
        <x-ui.loading-spinner class="text-primary-500 absolute -top-4 left-0 right-0 m-auto h-10 w-10 z-10" wire:loading="search,categoryFilter" />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" wire:loading.class="cursor-not-allowed select-none opacity-25">
            @forelse ($products as $product)
                <div wire:key="{{ $product->id }}"
                     class="group relative rounded-xl bg-dark-800/60 backdrop-blur-sm border border-dark-600/20
                            hover:border-primary-500/30 hover:-translate-y-1
                            transition-all duration-300 ease-out"
                     style="animation: card-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; animation-delay: {{ $loop->index * 0.08 }}s;">

                    <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                         style="background: radial-gradient(ellipse at 50% 0%, rgba(37,99,235,0.06) 0%, transparent 70%);">
                    </div>

                    <div class="relative px-5 py-4 border-b border-dark-600/20">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-dark-400 uppercase tracking-wider font-mono">{{ $product->code }}</p>
                                <p class="text-sm font-semibold text-dark-200 leading-snug truncate">{{ $product->name }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium shrink-0
                                @switch($product->category)
                                    @case('materia_prima') bg-amber-500 text-white border border-amber-400 @break
                                    @case('insumo') bg-blue-500 text-white border border-blue-400 @break
                                    @case('repuesto') bg-purple-500 text-white border border-purple-400 @break
                                    @case('combustible') bg-orange-500 text-white border border-orange-400 @break
                                    @default bg-dark-500 text-white border border-dark-400
                                @endswitch">
                                {{ $product->category_label }}
                            </span>
                        </div>
                    </div>

                    <div class="relative px-5 py-4 space-y-3">
                        @if ($product->description)
                            <p class="text-xs text-dark-400 leading-relaxed line-clamp-2">{{ $product->description }}</p>
                        @endif

                        <div class="flex items-center gap-2 pt-1">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/>
                            </svg>
                            <span class="font-mono font-semibold text-dark-200">{{ number_format($product->stock, 2, ',', '.') }}</span>
                            <span class="text-xs text-dark-400">/{{ $product->unit_label }}</span>
                        </div>
                    </div>

                    <div class="relative px-5 py-3 border-t border-dark-600/20 flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                            @if ($product->is_active)
                                bg-emerald-500 text-white border border-emerald-400
                            @else
                                bg-red-500 text-white border border-red-400
                            @endif">
                            <span class="w-1.5 h-1.5 rounded-full @if ($product->is_active) bg-white @else bg-white @endif"></span>
                            {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                        </span>

                        <div class="flex items-center gap-1">
                            <x-button.circle icon="eye" color="primary" light
                                :href="route('inventory.kardex', $product->id)" wire:navigate
                                title="Ver Kardex" />
                            @can('Editar productos')
                                <x-button.circle icon="pencil" color="blue" light
                                    :href="route('inventory.products.form', $product->id)" wire:navigate
                                    title="Editar" />
                            @endcan
                            @can('Eliminar productos')
                                <x-button.circle icon="trash" color="red" light
                                    onclick="confirmDelete('{{ $product->id }}')"
                                    title="Eliminar" />
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                    <div class="flex flex-col items-center justify-center gap-4 text-center py-16">
                        <div class="w-16 h-16 flex items-center justify-center rounded-2xl bg-dark-600/30 border border-dark-500/20">
                            <svg class="w-8 h-8 text-dark-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-dark-300">Sin productos</p>
                            <p class="text-sm text-dark-400 mt-1">No hay productos registrados.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="flex justify-center">
        {{ $products->links('ts-ui::components.table.paginators') }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        const component = Livewire.getByName('inventory.product-component')[0];

        $tsui.interaction('dialog')
            .wireable(component)
            .question('Advertencia', '¿Estás seguro de eliminar este producto?')
            .confirm('Confirmar', 'delete', [id])
            .cancel('Cancelar')
            .send();
    }
</script>
