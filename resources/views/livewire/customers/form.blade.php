<x-crud.modal entity="Cliente" :edit="$this->customerId">
    <x-input label="Nombre Completo" placeholder="Nombre completo" wire:model="full_name" />
    <x-input label="Cédula de Identidad" placeholder="C.I." wire:model="ci" />
    <x-upload label="Archivo (opcional)" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" wire:model="file" tip="Formatos: PDF, DOC, DOCX, JPG, PNG. Máx 5MB" />
    @if ($this->existingFile)
        <div class="mt-1 text-sm text-gray-500">
            Archivo actual:
            <a href="{{ \Storage::url($this->existingFile) }}" target="_blank" class="text-primary-600 hover:text-primary-800 underline">Ver archivo</a>
        </div>
    @endif
    <x-select.styled label="Cooperativa (opcional)" wire:model="cooperative_id" :options="\App\Models\Cooperative::select('id as value', 'name as label')->get()->toArray()" />
</x-crud.modal>
