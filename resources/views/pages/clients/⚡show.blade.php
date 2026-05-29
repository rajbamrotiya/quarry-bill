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

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl py-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:button icon="arrow-left" variant="ghost" :href="route('clients.index')" wire:navigate />
            <flux:heading size="xl">{{ __('Client Details') }}</flux:heading>
        </div>
        <flux:button icon="pencil-square" variant="primary" :href="route('clients.edit', $client)" wire:navigate>
            {{ __('Edit Client') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <flux:card class="lg:col-span-2 space-y-8">
            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Basic Information') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:text class="text-lg font-semibold">{{ $client->name }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email Address') }}</flux:label>
                        <flux:text>{{ $client->email ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Mobile Number') }}</flux:label>
                        <flux:text>{{ $client->mobile_number ?? '-' }}</flux:text>
                    </flux:field>
                </div>
            </section>

            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Location Details') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field class="md:col-span-2">
                        <flux:label>{{ __('Address') }}</flux:label>
                        <flux:text>{{ $client->address ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('District') }}</flux:label>
                        <flux:text>{{ $client->district ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('State') }}</flux:label>
                        <flux:text>{{ $client->state ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Country') }}</flux:label>
                        <flux:text>{{ $client->country ?? 'India' }}</flux:text>
                    </flux:field>
                </div>
            </section>

            @if($client->other_information)
                <section>
                    <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Other Information') }}</flux:heading>
                    <flux:text>{{ $client->other_information }}</flux:text>
                </section>
            @endif
        </flux:card>

        <flux:card class="space-y-8">
            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Tax Information') }}</flux:heading>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('GST Number') }}</flux:label>
                        <flux:badge variant="neutral" class="font-mono text-sm uppercase">{{ $client->gst_number ?? __('Not provided') }}</flux:badge>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('PAN Number') }}</flux:label>
                        <flux:badge variant="neutral" class="font-mono text-sm uppercase">{{ $client->pan_number ?? __('Not provided') }}</flux:badge>
                    </flux:field>
                </div>
            </section>

            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Metadata') }}</flux:heading>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Created At') }}</flux:label>
                        <flux:text class="text-sm">{{ $client->created_at->format('M d, Y H:i') }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Last Updated') }}</flux:label>
                        <flux:text class="text-sm">{{ $client->updated_at->format('M d, Y H:i') }}</flux:text>
                    </flux:field>
                </div>
            </section>
        </flux:card>
    </div>
</div>
