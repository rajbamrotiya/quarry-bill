<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;

new #[Title('Create User')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    public function save(): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Flux::toast(__('User created successfully.'));

        $this->redirect(route('users.index'), navigate: true);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-2xl py-6">
    <div class="flex items-center gap-2">
        <flux:button icon="arrow-left" variant="ghost" :href="route('users.index')" wire:navigate />
        <flux:heading size="xl">{{ __('Create User') }}</flux:heading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="name" :placeholder="__('Enter user name')" autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email Address') }}</flux:label>
                    <flux:input type="email" wire:model="email" :placeholder="__('Enter email address')" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Password') }}</flux:label>
                    <flux:input type="password" wire:model="password" :placeholder="__('Enter password')" />
                    <flux:error name="password" />
                </flux:field>
            </div>

            <div class="flex gap-2 pt-4">
                <flux:spacer />
                <flux:button :href="route('users.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Save User') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
