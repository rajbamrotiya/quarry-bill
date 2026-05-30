<?php

use App\Models\Receipt;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Receipts')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Computed]
    public function receipts()
    {
        return Receipt::query()
            ->with(['client', 'materialType'])
            ->withCount('histories')
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('vehicle_number', 'like', '%' . $this->search . '%')
                ->orWhere('pass_number', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Receipts') }}</flux:heading>
        <flux:button icon="plus" variant="primary" :href="route('receipts.create')" wire:navigate>
            {{ __('Create Receipt') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search receipts (Pass #, Client, Vehicle)...')" class="max-w-xs" />
    </div>

    <flux:card class="overflow-hidden p-0">
        <flux:table :paginate="$this->receipts">
            <flux:table.columns>
                <flux:table.column class="px-6">{{ __('Pass #') }}</flux:table.column>
                <flux:table.column>{{ __('Client') }}</flux:table.column>
                <flux:table.column>{{ __('Vehicle') }}</flux:table.column>
                <flux:table.column>{{ __('Material') }}</flux:table.column>
                <flux:table.column>{{ __('Date') }}</flux:table.column>
                <flux:table.column>{{ __('Net Weight') }}</flux:table.column>
                <flux:table.column class="px-6"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->receipts as $receipt)
                    <flux:table.row :key="$receipt->id">
                        <flux:table.cell class="px-6 font-medium">
                            <div class="flex items-center gap-2">
                                #{{ $receipt->pass_number ?: str_pad($receipt->id, 10, '0', STR_PAD_LEFT) }}
                                @if($receipt->histories_count > 1)
                                    <flux:tooltip content="{{ __('This receipt has been updated :count times', ['count' => $receipt->histories_count - 1]) }}" position="top">
                                        <flux:badge size="sm" color="amber" icon="pencil-square" class="px-1.5 py-0">
                                            {{ $receipt->histories_count - 1 }}
                                        </flux:badge>
                                    </flux:tooltip>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $receipt->client->name }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->vehicle_number }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->materialType->name }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->date->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->net_weight }} Ton</flux:table.cell>
                        <flux:table.cell class="px-6">
                            <div class="flex justify-end gap-2">
                                <flux:button icon="arrow-down-tray" variant="ghost" size="sm" :href="route('receipts.pdf', $receipt)" target="_blank" />
                                <flux:button icon="eye" variant="ghost" size="sm" :href="route('receipts.show', $receipt)" wire:navigate />
                                <flux:button icon="pencil-square" variant="ghost" size="sm" :href="route('receipts.edit', $receipt)" wire:navigate />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
