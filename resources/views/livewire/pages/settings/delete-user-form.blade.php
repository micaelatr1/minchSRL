<div>
    <p class="text-sm text-dark-400 mb-4">Una vez eliminada tu cuenta, todos tus datos serán eliminados permanentemente.</p>

    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-500 transition-all duration-200 shadow-lg shadow-red-600/20">
        Eliminar cuenta
    </button>

    <div x-cloak x-data="{ show: false }"
         x-show="show"
         x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
         x-on:close-modal.window="if ($event.detail === 'confirm-user-deletion') show = false"
         x-on:keydown.escape.window="show = false"
         class="fixed inset-0 z-50 flex items-center justify-center">
        <div x-show="show" x-transition.opacity
             class="fixed inset-0 bg-dark-900/60 backdrop-blur-sm"
             x-on:click="show = false"></div>
        <div x-show="show" x-transition
             class="relative bg-dark-800 border border-dark-600/20 rounded-xl p-6 w-full max-w-lg mx-4 shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-1">¿Estás seguro?</h3>
            <p class="text-sm text-dark-400 mb-5">Una vez eliminada tu cuenta, todos tus datos serán eliminados permanentemente. Ingresa tu contraseña para confirmar.</p>

            <form wire:submit="deleteUser" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-dark-300 mb-1">Contraseña</label>
                    <input wire:model="password" type="password" required autocomplete="current-password"
                           class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                    @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" x-on:click="show = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-dark-300 hover:text-white bg-dark-700/40 hover:bg-dark-700/60 border border-dark-600/20 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-500 transition-all duration-200 shadow-lg shadow-red-600/20">
                        Eliminar cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
