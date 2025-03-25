<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Ștergeți contul') }}</flux:heading>
        <flux:subheading>{{ __('Ștergeți-vă contul și toate resursele acestuia') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Ștergeți contul') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Sunteți sigur că doriți să vă ștergeți contul?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Odată ce contul dvs. este șters, toate resursele și datele acestuia vor fi șterse definitiv. Vă rugăm să introduceți parola pentru a confirma că doriți să vă ștergeți definitiv contul.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Password')" type="password" />

            <div class="flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Anulează') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Ștergeți contul') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
