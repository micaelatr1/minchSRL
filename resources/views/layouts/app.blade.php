<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        (function() {
            function getMode() {
                var stored = localStorage.getItem('dark-theme');
                if (stored === 'true' || stored === 'dark') return 'dark';
                if (stored === 'false' || stored === 'light') return 'light';
                if (stored === 'system') return 'system';
                return 'light';
            }
            function applyTheme() {
                var mode = getMode();
                var isDark = mode === 'dark' ? true : mode === 'light' ? false : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', isDark);
            }
            applyTheme();
            document.addEventListener('livewire:navigated', applyTheme);
            window.__theme = { getMode: getMode, setMode: function(type) { localStorage.setItem('dark-theme', type); applyTheme(); } };
        })();
    </script>

    <tallstackui:script />
    @livewireStyles
    @vite('resources/js/app.js')
</head>

<body class="app-layout font-sans antialiased bg-gray-100 dark:bg-dark-900" x-cloak x-data="{ name: @js(auth()->user()->name) }" x-on:name-updated.window="name = $event.detail.name">
    <x-layout>
        <x-slot:top>
            <x-dialog />
            <x-toast />
        </x-slot:top>
        <x-slot:header>
            <x-layout.header>
                <x-slot:right>
                    <div class="flex items-center gap-4">
                        <div x-data="{ mode: window.__theme.getMode() }" class="flex items-center gap-1 rounded-lg bg-gray-100 dark:bg-dark-800 p-1">
                            <button type="button" x-on:click="mode = 'dark'; window.__theme.setMode('dark')" x-bind:class="{ 'bg-white dark:bg-dark-700': mode === 'dark', 'text-gray-500 hover:text-gray-700 dark:text-dark-300 dark:hover:text-dark-100': mode !== 'dark' }" class="cursor-pointer rounded-md p-1.5 transition-colors">
                                <svg class="h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd"/></svg>
                            </button>
                            <button type="button" x-on:click="mode = 'system'; window.__theme.setMode('system')" x-bind:class="{ 'bg-white dark:bg-dark-700': mode === 'system', 'text-gray-500 hover:text-gray-700 dark:text-dark-300 dark:hover:text-dark-100': mode !== 'system' }" class="cursor-pointer rounded-md p-1.5 transition-colors">
                                <svg class="h-4 w-4 text-gray-500 dark:text-dark-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd"/></svg>
                            </button>
                            <button type="button" x-on:click="mode = 'light'; window.__theme.setMode('light')" x-bind:class="{ 'bg-white dark:bg-dark-700': mode === 'light', 'text-gray-500 hover:text-gray-700 dark:text-dark-300 dark:hover:text-dark-100': mode !== 'light' }" class="cursor-pointer rounded-md p-1.5 transition-colors">
                                <svg class="h-4 w-4 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z"/></svg>
                            </button>
                        </div>
                        <x-dropdown>
                            <x-slot:action>
                                <button class="flex items-center gap-2 cursor-pointer rounded-full p-1 hover:bg-gray-100 dark:hover:bg-dark-700 transition-colors duration-200" x-on:click="show = !show">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-white text-sm font-bold" x-text="name ? name.charAt(0).toUpperCase() : '?'"></span>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-dark-200 hidden sm:block" x-text="name"></span>
                                    <x-icon name="chevron-down" class="h-4 w-4 text-gray-400 dark:text-dark-300" />
                                </button>
                            </x-slot:action>
                            <x-slot:header>
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-dark-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-dark-100" x-text="name"></p>
                                    <p class="text-xs text-gray-500 dark:text-dark-400 mt-1">{{ auth()->user()->email }}</p>
                                </div>
                            </x-slot:header>
                            <x-dropdown.items :text="__('Profile')" icon="user-circle" :href="route('profile.edit')" />
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown.items :text="__('Logout')" icon="arrow-right-on-rectangle"
                                    onclick="event.preventDefault(); this.closest('form').submit();" separator />
                            </form>
                        </x-dropdown>
                    </div>
                </x-slot:right>
            </x-layout.header>
        </x-slot:header>
        <x-slot:menu>
            <x-side-bar smart collapsible thin-scroll>
                <x-slot:brand>
                    <div class="px-5 py-5 flex flex-col items-center justify-center border-b border-gray-200 dark:border-dark-700">
                        <img src="{{ asset('image/logo.png') }}" width="100" height="100" alt="Logo" class="mb-3" />
                        <h1 class="font-sans text-lg font-bold text-center tracking-tight text-gray-900 dark:text-white leading-tight">
                            MINCH SRL.
                        </h1>
                        <p class="text-[11px] text-gray-500 dark:text-dark-400 mt-0.5">Sistema de Gestión</p>
                    </div>
                </x-slot:brand>
                <x-slot:brand-collapsed>
                    <div class="mt-5 mb-3 flex flex-col items-center justify-center">
                        <img src="{{ asset('image/logo.png') }}" width="44" height="44" alt="Logo" class="mb-1" />
                        <h1 class="text-gray-700 dark:text-dark-200 text-[10px] text-center font-bold leading-tight">
                            MINCH
                        </h1>
                    </div>
                </x-slot:brand-collapsed>
                @can('Ver dashboard')
                    
                <x-side-bar.item text="Dashboard" icon="home" :route="route('dashboard')" wire:navigate />
                @endcan
                 <x-side-bar.item text="Control de Usuario" icon="shield-check">
                    @can('Ver usuarios')
                    <x-side-bar.item text="Usuarios" icon="user" :route="route('users')" wire:navigate/>
                    @endcan
                    @can('Ver roles')
                    <x-side-bar.item text="Roles" icon="user-group" :route="route('roles')" wire:navigate/>
                    @endcan
                    @can('Asignación de permisos')
                    <x-side-bar.item text="Permisos" icon="key" :route="route('permissions')" wire:navigate/>
                    @endcan
                </x-side-bar.item>
                @can('Ver impuestos')
                <x-side-bar.item text="Impuestos" icon="percent-badge" :route="route('taxes')" wire:navigate />
                @endcan
                @can('Ver proveedores')
                <x-side-bar.item text="Proveedores" icon="truck" :route="route('suppliers')" wire:navigate />
                @endcan
                @can('Ver clientes')
                <x-side-bar.item text="Clientes" icon="users" :route="route('customers')" wire:navigate />
                @endcan
                {{-- @can('Ver departamentos')
                <x-side-bar.item text="Departamentos" icon="building-office-2" :route="route('departments')" wire:navigate />
                @endcan --}}
                @can('Ver cooperativas')
                <x-side-bar.item text="Cooperativas" icon="building-storefront" :route="route('cooperatives')" wire:navigate />
                @endcan
                 @can('Ver cotizaciones') 
                <x-side-bar.item text="Cotizaciones" icon="currency-dollar" :route="route('cotizaciones')" wire:navigate />
                @endcan 
                @can('Ver cuentas')
                <x-side-bar.item text="Cuentas" icon="book-open"  :route="route('accounts')" wire:navigate />
                @endcan
                @can('Ver retenciones')
                <x-side-bar.item text="Retenciones" icon="hand-raised" :route="route('retentions')" wire:navigate />
                @endcan
                <x-side-bar.separator line text="Finanzas" />
                <x-side-bar.item text="Tesoreria" icon="credit-card">
                    @can('Ver libro de bancos')
                    <x-side-bar.item text="Libro de bancos" icon="book-open" :route="route('transactions')" wire:navigate />
                    @endcan
                    @can('Ver caja chica')
                    <x-side-bar.item text="Libro de cajas" icon="archive-box" :route="route('accounts.box')" wire:navigate />
                    @endcan
                    @can('Ver estados de cuenta')
                    <x-side-bar.item text="Estado de cuenta" icon="document-text" :route="route('accounts.statement')" wire:navigate />
                    @endcan
                      @can('Ver contratos')
                    <x-side-bar.item text="Contratos" icon="clipboard-document-check" :route="route('contracts')" wire:navigate />
                    @endcan
                </x-side-bar.item>
                @can('Ver productos')
                <x-side-bar.item text="Inventario" icon="archive-box">
                    @can('Ver entradas inventario')
                    <x-side-bar.item text="Compras" icon="shopping-cart" :route="route('inventory.purchases')" wire:navigate />
                    @endcan
                    @can('Ver productos')
                    <x-side-bar.item text="Producto" icon="cube" :route="route('inventory.products')" wire:navigate />
                    @endcan
                    @can('Ver banco inventario')
                    <x-side-bar.item text="Banco" icon="building-library" :route="route('inventory.bank')" wire:navigate />
                    @endcan
                </x-side-bar.item>
                @endcan
                @can('Ver liquidaciones')
                    
                <x-side-bar.item text="Liquidaciones" icon="calculator" :route="route('liquidations')" wire:navigate />
                @endcan
                <x-side-bar.separator line text="RRHH" />
                @can('Ver empleados')
                <x-side-bar.item text="RRHH" icon="users">
                    @can('Ver empleados')
                    <x-side-bar.item text="Empleados" icon="identification" :route="route('rrhh.employees')" wire:navigate />
                    @endcan
                    @can('Ver planillas')
                    <x-side-bar.item text="Planilla de Sueldos" icon="currency-dollar" :route="route('rrhh.payrolls')" wire:navigate />
                    @endcan
                    @can('Ver vacaciones')
                    <x-side-bar.item text="Vacaciones" icon="calendar-days" :route="route('rrhh.vacations')" wire:navigate />
                    @endcan
                </x-side-bar.item>
                @endcan
              @can('Ver reportes')
                <x-side-bar.separator line text="Reportes" />
                <x-side-bar.item text="Reportes" icon="chart-bar">
                    <x-side-bar.item text="Retenciones" icon="document-arrow-down" :route="route('reports.retentions')" wire:navigate />
                    <x-side-bar.item text="Caja" icon="archive-box" :route="route('reports.box')" wire:navigate />
                    <x-side-bar.item text="Banco" icon="book-open" :route="route('reports.bank-book')" wire:navigate />
                     <x-side-bar.item text="Liquidaciones" icon="calculator" :route="route('reports.liquidations')" wire:navigate />
               </x-side-bar.item>
                @endcan
            </x-side-bar>
        </x-slot:menu>
        <div wire:navigate.main class="page-enter">
            {{ $slot }}
        </div>
    </x-layout>
    @livewireScripts
    
</body>
<script>
    function confirmDelete(id) {
        const component = Livewire.getByName('nombre-de-tu-componente')[0];

        $tsui.interaction('dialog')
            .wireable(component)
            .question('Advertencia', '¿Estás seguro de eliminar este registro?')
            .confirm('Confirmar', 'delete', [id])
            .cancel('Cancelar')
            .send();
    }
</script>
</html>
