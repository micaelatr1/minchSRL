<x-crud.modal entity="Cliente" :edit="$this->customerId">
    <x-input label="Nombre Completo" placeholder="Nombre completo" wire:model="full_name" />
    @error('full_name') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
    <x-input label="Cédula de Identidad" placeholder="C.I." wire:model="ci" />
    @error('ci') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
    <x-select.styled label="Tipo (opcional)" wire:model="tipo" :options="[
        ['label' => 'Patente', 'value' => 'PATENTE'],
        ['label' => 'Contrato', 'value' => 'CONTRATO'],
    ]" />
    @error('tipo') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
    <x-input label="Código (opcional)" placeholder="Código" wire:model="codigo" />
    @error('codigo') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
    <x-date label="Fecha (opcional)" wire:model="fecha" />
    @error('fecha') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
    <x-upload label="Archivo (opcional)" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" wire:model="file" tip="Formatos: PDF, DOC, DOCX, JPG, PNG. Máx 5MB" />
    @error('file') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
    @if ($this->existingFile)
        <div class="mt-1 text-sm text-gray-500">
            Archivo actual:
            <a href="{{ \Storage::url($this->existingFile) }}" target="_blank" class="text-primary-600 hover:text-primary-800 underline">Ver archivo</a>
        </div>
    @endif
    <x-select.styled label="Cooperativa (opcional)" wire:model="cooperative_id" :options="$this->cooperativeOptions" />
    @error('cooperative_id') <span class="mt-1 block text-sm font-medium text-red-500">{{ $message }}</span> @enderror
</x-crud.modal>
