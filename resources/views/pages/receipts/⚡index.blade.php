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

    public function delete(Receipt $receipt): void
    {
        $receipt->delete();

        Flux::toast(__('Receipt deleted successfully.'));
    }

    #[Computed]
    public function receipts()
    {
        return Receipt::query()
            ->with(['client', 'materialType'])
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('vehicle_number', 'like', '%' . $this->search . '%')
                ->orWhere('id', 'like', '%' . $this->search . '%');
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
                <flux:table.column>{{ __('Pass #') }}</flux:table.column>
                <flux:table.column>{{ __('Client') }}</flux:table.column>
                <flux:table.column>{{ __('Vehicle') }}</flux:table.column>
                <flux:table.column>{{ __('Material') }}</flux:table.column>
                <flux:table.column>{{ __('Date') }}</flux:table.column>
                <flux:table.column>{{ __('Net Weight') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->receipts as $receipt)
                    <flux:table.row :key="$receipt->id">
                        <flux:table.cell class="font-medium">#{{ $receipt->id }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->client->name }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->vehicle_number }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->materialType->name }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->date->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $receipt->net_weight }} Ton</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end gap-2">
                                <flux:button icon="arrow-down-tray" variant="ghost" size="sm" :href="route('receipts.pdf', $receipt)" target="_blank" />
                                <flux:button icon="eye" variant="ghost" size="sm" :href="route('receipts.show', $receipt)" wire:navigate />
                                <flux:button icon="pencil-square" variant="ghost" size="sm" :href="route('receipts.edit', $receipt)" wire:navigate />
                                <flux:modal.trigger name="delete-receipt-{{ $receipt->id }}">
                                    <flux:button icon="trash" variant="ghost" size="sm" />
                                </flux:modal.trigger>

                                <flux:modal name="delete-receipt-{{ $receipt->id }}" class="max-w-sm">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Delete Receipt') }}</flux:heading>
                                            <flux:subheading>{{ __('Are you sure you want to delete this receipt? This action cannot be undone.') }}</flux:subheading>
                                        </div>

                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button type="submit" variant="danger" wire:click="delete({{ $receipt->id }})">{{ __('Delete') }}</flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
