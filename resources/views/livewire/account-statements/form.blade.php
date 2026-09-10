<x-crud.modal entity="Titular" :title="$holderLabel ?? 'Titular'" :edit="$this->id">
    <x-input label="Nombre Completo" placeholder="Nombre completo" wire:model="full_name" />
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="w-full sm:w-1/2">
            <x-input label="Cédula de Identidad" placeholder="Ej. 1234567" wire:model="ci" />
        </div>
        <div class="w-full sm:w-1/2">
            <x-input label="Teléfono" placeholder="Ej. 77731123" wire:model="phone" />
        </div>
    </div>
    <x-select.styled label="Cooperativa (opcional)" :options="$options" wire:model="cooperative_id" />
    <x-upload label="Contrato / documento" accept=".pdf,.doc,.docx" wire:model="file" tip="Arrastre el archivo aquí" />
</x-crud.modal>
