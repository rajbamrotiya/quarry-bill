<?php

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Edit Client')] class extends Component {
    public Client $client;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public string $mobile_number = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    public function mount(Client $client): void
    {
        $this->client = $client;
        $this->name = $client->name;
        $this->mobile_number = $client->mobile_number ?? '';
        $this->email = $client->email ?? '';
    }

    public function save(): void
    {
        $this->validate();

        $this->client->update([
            'name' => $this->name,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
        ]);

        Flux::toast(__('Client updated successfully.'));

        $this->redirect(route('clients.index'), navigate: true);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-2xl">
    <div class="flex items-center gap-2">
        <flux:button icon="arrow-left" variant="ghost" :href="route('clients.index')" wire:navigate />
        <flux:heading size="xl">{{ __('Edit Client') }}</flux:heading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" :placeholder="__('Enter client name')" autofocus />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Mobile Number') }}</flux:label>
                <flux:input wire:model="mobile_number" :placeholder="__('Enter mobile number')" />
                <flux:error name="mobile_number" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Email Address') }}</flux:label>
                <flux:input type="email" wire:model="email" :placeholder="__('Enter email address')" />
                <flux:error name="email" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button :href="route('clients.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Update Client') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
