<div class="relative">
    <x-input 
        label="Buscar proveedor (CI o nombre)"
        placeholder="Escriba CI o nombre..."
        wire:model.live.debounce.300ms="searchTerm"
        wire:focus="showDropdown"
        wire:blur="hideDropdown"
        class="w-full"
    />

    @if ($showList && count($filteredSuppliers) > 0)
        <ul class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            @foreach ($filteredSuppliers as $person)
                <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                    wire:click="selectSupplier({{ $person->id }})">
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $person->full_name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">CI: {{ $person->ci }}</div>
                    @if (isset($person->phone))
                        <div class="text-xs text-gray-400">Tel: {{ $person->phone }}</div>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>