<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('Un link de resetare va fi trimis dacă contul există.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Parolă uitată')" :description="__('Introduceți adresa dvs. de e-mail pentru a primi un link de resetare a parolei')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            required
            autofocus
            placeholder="email@anp.gov.md"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Link de resetare a parolei de e-mail') }}</flux:button>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-400">
        {{ __('Sau, reveniți la') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('autentificare') }}</flux:link>
    </div>
</div>
