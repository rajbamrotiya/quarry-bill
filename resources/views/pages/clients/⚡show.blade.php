<?php

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Client Details')] class extends Component {
    public Client $client;

    public function mount(Client $client): void
    {
        $this->client = $client;
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-2xl">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:button icon="arrow-left" variant="ghost" :href="route('clients.index')" wire:navigate />
            <flux:heading size="xl">{{ __('Client Details') }}</flux:heading>
        </div>
        <flux:button icon="pencil-square" variant="primary" :href="route('clients.edit', $client)" wire:navigate>
            {{ __('Edit Client') }}
        </flux:button>
    </div>

    <flux:card class="space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:text class="font-medium">{{ $client->name }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Email Address') }}</flux:label>
                <flux:text>{{ $client->email ?? '-' }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Mobile Number') }}</flux:label>
                <flux:text>{{ $client->mobile_number ?? '-' }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Created At') }}</flux:label>
                <flux:text>{{ $client->created_at->format('M d, Y H:i') }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Last Updated') }}</flux:label>
                <flux:text>{{ $client->updated_at->format('M d, Y H:i') }}</flux:text>
            </flux:field>
        </div>
    </flux:card>
</div>
