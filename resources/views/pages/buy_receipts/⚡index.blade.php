<?php

use App\Models\BuyReceipt;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('BuyBuyReceipts')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Computed]
    public function buy_receipts()
    {
        return BuyReceipt::query()
            ->with(['supplier', 'materialType'])
            ->withCount('histories')
            ->when($this->search, function ($query) {
                $query->whereHas('supplier', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('vehicle_number', 'like', '%' . $this->search . '%')
                ->orWhere('pass_number', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    public function delete(BuyReceipt $buy_receipt)
    {
        $buy_receipt->delete();
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('BuyBuyReceipts') }}</flux:heading>
        <flux:button icon="plus" variant="primary" :href="route('buy-receipts.create')" wire:navigate>
            {{ __('Create BuyReceipt') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search buy_receipts (Pass #, Supplier, Vehicle)...')" class="max-w-xs" />
    </div>

    <flux:card class="overflow-hidden p-0">
        <flux:table :paginate="$this->buy_receipts">
            <flux:table.columns>
                <flux:table.column class="px-6">{{ __('ID') }}</flux:table.column>
                <flux:table.column>{{ __('Pass #') }}</flux:table.column>
                <flux:table.column>{{ __('Supplier') }}</flux:table.column>
                <flux:table.column>{{ __('Vehicle') }}</flux:table.column>
                <flux:table.column>{{ __('Material') }}</flux:table.column>
                <flux:table.column>{{ __('Date') }}</flux:table.column>
                <flux:table.column>{{ __('Net Weight') }}</flux:table.column>
                <flux:table.column class="px-6"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->buy_receipts as $buy_receipt)
                    <flux:table.row :key="$buy_receipt->id">
                        <flux:table.cell class="px-6 text-zinc-500 font-medium">{{ $buy_receipt->id }}</flux:table.cell>
                        <flux:table.cell class="font-medium">
                            <div class="flex items-center gap-2">
                                #{{ $buy_receipt->pass_number ?: str_pad($buy_receipt->id, 10, '0', STR_PAD_LEFT) }}
                                @if($buy_receipt->histories_count > 1)
                                    <flux:tooltip content="{{ __('This buy_receipt has been updated :count times', ['count' => $buy_receipt->histories_count - 1]) }}" position="top">
                                        <flux:badge size="sm" color="amber" icon="pencil-square" class="px-1.5 py-0">
                                            {{ $buy_receipt->histories_count - 1 }}
                                        </flux:badge>
                                    </flux:tooltip>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $buy_receipt->supplier->name }}</flux:table.cell>
                        <flux:table.cell>{{ $buy_receipt->vehicle_number }}</flux:table.cell>
                        <flux:table.cell>{{ $buy_receipt->materialType->name }}</flux:table.cell>
                        <flux:table.cell>{{ $buy_receipt->date->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $buy_receipt->net_weight }} KG</flux:table.cell>
                        <flux:table.cell class="px-6">
                            <div class="flex justify-end gap-2">
                                <flux:button icon="arrow-down-tray" variant="ghost" size="sm" :href="route('buy-receipts.pdf', $buy_receipt)" target="_blank" />
                                <flux:button icon="eye" variant="ghost" size="sm" :href="route('buy-receipts.show', $buy_receipt)" wire:navigate />
                                <flux:button icon="pencil-square" variant="ghost" size="sm" :href="route('buy-receipts.edit', $buy_receipt)" wire:navigate />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
