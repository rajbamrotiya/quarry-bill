<?php

use App\Models\MaterialType;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Material Types')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    public function delete(MaterialType $materialType): void
    {
        if ($materialType->receipts()->exists()) {
            Flux::toast(__('Cannot delete material type as it is linked to existing receipts.'), variant: 'danger');
            return;
        }

        $materialType->delete();

        Flux::toast(__('Material type deleted successfully.'));
    }

    #[Computed]
    public function materialTypes()
    {
        return MaterialType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('hsn_code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Material Types') }}</flux:heading>
        <flux:button icon="plus" variant="primary" :href="route('material-types.create')" wire:navigate>
            {{ __('Add Material Type') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search materials...')" class="max-w-xs" />
    </div>

    <flux:card class="overflow-hidden p-0">
        <flux:table :paginate="$this->materialTypes">
            <flux:table.columns>
                <flux:table.column class="px-6">{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('HSN Code') }}</flux:table.column>
                <flux:table.column>{{ __('Unit Rate') }}</flux:table.column>
                <flux:table.column>{{ __('Created At') }}</flux:table.column>
                <flux:table.column class="px-6"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->materialTypes as $type)
                    <flux:table.row :key="$type->id">
                        <flux:table.cell class="px-6 font-medium text-zinc-800 dark:text-zinc-200">{{ $type->name }}</flux:table.cell>
                        <flux:table.cell>{{ $type->hsn_code ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $type->unit_rate ? '₹' . number_format($type->unit_rate, 2) : '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $type->created_at->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell class="px-6">
                            <div class="flex justify-end gap-2">
                                <flux:button icon="pencil-square" variant="ghost" size="sm" :href="route('material-types.edit', $type)" wire:navigate />
                                
                                <flux:modal.trigger name="delete-material-{{ $type->id }}">
                                    <flux:button icon="trash" variant="ghost" size="sm" />
                                </flux:modal.trigger>

                                <flux:modal name="delete-material-{{ $type->id }}" class="max-w-sm">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Delete Material Type') }}</flux:heading>
                                            <flux:subheading>{{ __('Are you sure you want to delete this material? This action cannot be undone.') }}</flux:subheading>
                                        </div>

                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button type="submit" variant="danger" wire:click="delete({{ $type->id }})">{{ __('Delete') }}</flux:button>
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
