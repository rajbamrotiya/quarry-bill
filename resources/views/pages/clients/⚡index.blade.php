<?php

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Clients')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    public function delete(Client $client): void
    {
        $client->delete();

        Flux::toast(__('Client deleted successfully.'));
    }

    #[Computed]
    public function clients()
    {
        return Client::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $this->search . '%')
                    ->orWhere('gst_number', 'like', '%' . $this->search . '%')
                    ->orWhere('district', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Clients') }}</flux:heading>
        <flux:button icon="plus" variant="primary" :href="route('clients.create')" wire:navigate>
            {{ __('Create Client') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by name, email, mobile, GST or district...')" class="max-w-md" />
    </div>

    <flux:card class="overflow-hidden p-0">
        <flux:table :paginate="$this->clients">
            <flux:table.columns>
                <flux:table.column class="px-6">{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Contact') }}</flux:table.column>
                <flux:table.column>{{ __('Location') }}</flux:table.column>
                <flux:table.column>{{ __('GSTIN') }}</flux:table.column>
                <flux:table.column class="px-6"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->clients as $client)
                    <flux:table.row :key="$client->id">
                        <flux:table.cell class="px-6 font-medium">
                            <div class="flex flex-col">
                                <span>{{ $client->name }}</span>
                                <span class="text-xs text-zinc-500 font-normal">{{ $client->email }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $client->mobile_number ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $client->district ?? '-' }}</span>
                                <span class="text-xs text-zinc-500">{{ $client->state ?? '-' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge variant="neutral" class="text-xs uppercase font-mono">{{ $client->gst_number ?? '-' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="px-6">
                            <div class="flex justify-end gap-2">
                                <flux:button icon="eye" variant="ghost" size="sm" :href="route('clients.show', $client)" wire:navigate />
                                <flux:button icon="pencil-square" variant="ghost" size="sm" :href="route('clients.edit', $client)" wire:navigate />
                                <flux:modal.trigger name="delete-client-{{ $client->id }}">
                                    <flux:button icon="trash" variant="ghost" size="sm" />
                                </flux:modal.trigger>

                                <flux:modal name="delete-client-{{ $client->id }}" class="max-w-sm">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Delete Client') }}</flux:heading>
                                            <flux:subheading>{{ __('Are you sure you want to delete this client? This action cannot be undone.') }}</flux:subheading>
                                        </div>

                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button type="submit" variant="danger" wire:click="delete({{ $client->id }})">{{ __('Delete') }}</flux:button>
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
