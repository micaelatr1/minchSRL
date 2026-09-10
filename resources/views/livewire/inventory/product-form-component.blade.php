<div>
    <x-card>
        <x-slot:header>
            <div class="flex items-center gap-3">
                <a href="{{ route('inventory.products') }}" wire:navigate
                   class="inline-flex items-center gap-2 text-sm font-medium text-dark-300 hover:text-white transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Volver
                </a>
                <span class="text-dark-400">|</span>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $id ? 'Editar Producto' : 'Nuevo Producto' }}
                </h2>
            </div>
        </x-slot:header>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input label="Nombre del producto" wire:model="name" required />
                    @error('name') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-select.styled label="Categoría" wire:model="category" required :options="[
                        ['label' => 'Materia Prima', 'value' => 'materia_prima'],
                        ['label' => 'Insumo', 'value' => 'insumo'],
                        ['label' => 'Repuesto', 'value' => 'repuesto'],
                        ['label' => 'Combustible', 'value' => 'combustible'],
                        ['label' => 'Otro', 'value' => 'otro'],
                    ]" />
                    @error('category') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-select.styled label="Unidad de Medida" wire:model="unit_of_measure" required :options="[
                        ['label' => 'Kilogramo (Kg)', 'value' => 'kg'],
                        ['label' => 'Tonelada (Ton)', 'value' => 'ton'],
                        ['label' => 'Litro (L)', 'value' => 'l'],
                        ['label' => 'Unidad (Unid)', 'value' => 'u'],
                        ['label' => 'Metro (M)', 'value' => 'm'],
                    ]" />
                    @error('unit_of_measure') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-toggle label="Producto activo" wire:model="is_active" position="left" />
                </div>

                <div class="md:col-span-2">
                    <x-textarea label="Descripción" wire:model="description" />
                    @error('description') <span class="text-sm text-red-400">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('inventory.products') }}" wire:navigate
                   class="px-4 py-2 rounded-lg text-sm font-semibold text-dark-300 bg-dark-700 hover:bg-dark-600 transition-all duration-200">
                    Cancelar
                </a>
                <x-button type="submit" color="primary" loading="save">
                    {{ $id ? 'Actualizar' : 'Guardar' }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
