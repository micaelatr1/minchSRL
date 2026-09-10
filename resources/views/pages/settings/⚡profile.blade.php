<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Perfil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $last_name = '';
    public string $ci = '';
    public string $email = '';
    public string $phone = '';
    public string $gender = '';
    public string $birthdate = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->last_name = $user->last_name;
        $this->ci = $user->ci;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->gender = $user->gender;
        $this->birthdate = $user->birthdate;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Perfil')" :subheading="__('Actualiza tu información personal')">
        <div class="space-y-6">

            {{-- Avatar card --}}
            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-5 flex items-center gap-4">
                <div class="w-14 h-14 flex items-center justify-center rounded-full bg-primary-600/20 border border-primary-500/30 text-primary-400 text-xl font-bold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-sm text-dark-400">{{ Auth::user()->email }}</p>
                </div>
            </div>

            {{-- Profile form card --}}
            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-5">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Información personal</h2>

                <form wire:submit="updateProfileInformation" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Nombre</label>
                            <input wire:model="name" type="text" required
                                   class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                            @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Apellido</label>
                            <input wire:model="last_name" type="text" required
                                   class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                            @error('last_name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Cédula de Identidad</label>
                            <input wire:model="ci" type="text" required
                                   class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                            @error('ci') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Email</label>
                            <input wire:model="email" type="email" required autocomplete="email"
                                   class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                            @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            @if ($this->hasUnverifiedEmail)
                                <p class="mt-1 text-xs text-dark-400">
                                    Tu email no está verificado.
                                    <a wire:click.prevent="resendVerificationNotification" class="text-primary-400 hover:text-primary-300 cursor-pointer underline">Reenviar verificación</a>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-1 text-xs text-green-400">Nuevo enlace enviado.</p>
                                @endif
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Teléfono</label>
                            <input wire:model="phone" type="text" required maxlength="10"
                                   class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                            @error('phone') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Género</label>
                            <select wire:model="gender" required
                                    class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors">
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            @error('gender') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-1">Fecha de nacimiento</label>
                            <input wire:model="birthdate" type="date" required
                                   class="w-full rounded-lg border border-dark-600/20 bg-dark-700/40 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-dark-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none transition-colors" />
                            @error('birthdate') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition-all duration-200 shadow-lg shadow-primary-600/20">
                            Guardar cambios
                        </button>
                        <x-action-message on="profile-updated">
                            <span class="text-sm text-emerald-400 font-medium">✓ Guardado</span>
                        </x-action-message>
                    </div>
                </form>
            </div>

            {{-- Roles card --}}
            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-dark-600/20 p-5">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Roles asignados</h2>
                <div class="flex flex-wrap gap-2">
                    @forelse (Auth::user()->roles as $role)
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-primary-600/10 text-primary-600 dark:text-primary-400 border border-primary-500/20">
                            {{ $role->name }}
                        </span>
                    @empty
                        <p class="text-sm text-dark-400">Sin roles asignados</p>
                    @endforelse
                </div>
            </div>

            {{-- Delete account card --}}
            @if ($this->showDeleteUser)
            <div class="bg-dark-800/40 backdrop-blur-sm rounded-xl border border-red-500/20 p-5">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Zona de peligro</h2>
                <p class="text-sm text-dark-400 mb-4">Una vez eliminada tu cuenta, todos tus datos serán eliminados permanentemente.</p>
                <livewire:pages::settings.delete-user-form />
            </div>
            @endif

        </div>
    </x-pages::settings.layout>
</section>
