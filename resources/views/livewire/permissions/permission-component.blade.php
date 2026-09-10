<div class="space-y-4">
    <div class="flex flex-col sm:flex-row gap-4 sm:justify-end sm:items-end">
        <x-button color="green" icon="plus" class="w-full sm:w-auto" outline 
        wire:click="syncAll()"> Asignar
            todo</x-button>
        <x-button color="red" icon="x-mark" class="w-full sm:w-auto" outline 
        wire:click="revokeAll()">Revocar
            todo</x-button>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-end">

        <div>
            <x-select.styled :options="$options" wire:model.live="role" />
        </div>
        <div>
            <x-input icon="magnifying-glass-circle" placeholder="Buscar" wire:model.live="search" />
        </div>

    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach ($permissions as $permission)
            <x-card>
                <x-slot:header>
                        <h5>{{ $permission->name }}</h5>
                        <x-button :icon="$permission->checked ? 'check' : 'x-mark'" :color="$permission->checked ? 'green' : 'red'" outline size="xs" circle flat
                            wire:click="syncPermission({{ $permission->checked ? 0 : 1 }}, '{{ $permission->name }}')"
                            wire:loading.attr="disabled" data-toggle="tooltip" data-placement="top" :title="$permission->checked ? 'Revocar permiso' : 'Asignar permiso'" />
                </x-slot:header>

                <p>
                    {{ $permission->description }}
                </p>
            </x-card>
        @endforeach
    </div>
    {{--     {{ $permissions->links('ts-ui::components.table.paginators') }} --}}
</div>
