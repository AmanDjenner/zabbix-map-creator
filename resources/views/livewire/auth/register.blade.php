<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Creați un cont')" :description="__('Introdu detaliile mai jos pentru a-ți crea contul')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Nume')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('NNP')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Adresa de Email')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@anp.gov.md"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Parola')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Parola')"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirmați parola')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirmați parola')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Creează cont') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Aveți deja un cont?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Autentificare') }}</flux:link>
    </div>
</div>
