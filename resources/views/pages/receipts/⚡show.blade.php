<?php

use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Receipt Details')] class extends Component {
    public Receipt $receipt;

    public function mount(Receipt $receipt): void
    {
        $this->receipt = $receipt->load(['client', 'materialType']);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-2xl">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:button icon="arrow-left" variant="ghost" :href="route('receipts.index')" wire:navigate />
            <flux:heading size="xl">{{ __('Receipt Details') }}</flux:heading>
        </div>
        <div class="flex gap-2">
            <flux:button icon="arrow-down-tray" variant="ghost" :href="route('receipts.pdf', $receipt)" target="_blank">
                {{ __('Download PDF') }}
            </flux:button>
            <flux:button icon="pencil-square" variant="primary" :href="route('receipts.edit', $receipt)" wire:navigate>
                {{ __('Edit Receipt') }}
            </flux:button>
        </div>
    </div>

    <flux:card class="space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <flux:field>
                <flux:label>{{ __('Pass Number') }}</flux:label>
                <flux:text class="font-medium">#{{ $receipt->id }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Client') }}</flux:label>
                <flux:text class="font-medium">{{ $receipt->client->name }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Vehicle Number') }}</flux:label>
                <flux:text>{{ $receipt->vehicle_number }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Material Type') }}</flux:label>
                <flux:text>{{ $receipt->materialType->name }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Date') }}</flux:label>
                <flux:text>{{ $receipt->date->format('M d, Y') }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Time') }}</flux:label>
                <flux:text>{{ $receipt->time }}</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Gross Weight') }}</flux:label>
                <flux:text>{{ $receipt->gross_weight }} Ton</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Tare Weight') }}</flux:label>
                <flux:text>{{ $receipt->tare_weight }} Ton</flux:text>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Net Weight') }}</flux:label>
                <flux:text class="font-bold">{{ $receipt->net_weight }} Ton</flux:text>
            </flux:field>
        </div>

        @if ($receipt->remarks)
            <flux:field>
                <flux:label>{{ __('Remarks') }}</flux:label>
                <flux:text>{{ $receipt->remarks }}</flux:text>
            </flux:field>
        @endif
    </flux:card>
</div>
