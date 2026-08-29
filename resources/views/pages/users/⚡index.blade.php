<?php

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Users')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    /*public function delete(User $user): void
    {
        $user->delete();

        Flux::toast(__('User deleted successfully.'));
    }*/

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Users') }}</flux:heading>
        <flux:button icon="plus" variant="primary" :href="route('users.create')" wire:navigate>
            {{ __('Create User') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by name or email...')" class="max-w-md" />
    </div>

    <flux:card class="overflow-hidden p-0 border-none shadow-none bg-transparent">
        <flux:table :paginate="$this->users">
            <flux:table.columns>
                <flux:table.column class="px-6">{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Email') }}</flux:table.column>
                <flux:table.column class="px-6"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->users as $user)
                    <flux:table.row :key="$user->id">
                        <flux:table.cell class="px-6 font-medium">
                            {{ $user->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $user->email }}
                        </flux:table.cell>
                        <flux:table.cell class="px-6">
                            <div class="flex justify-end gap-2">
                                <flux:button icon="pencil-square" variant="ghost" size="sm" :href="route('users.edit', $user)" wire:navigate />
                                {{--<flux:modal.trigger name="delete-user-{{ $user->id }}">
                                    <flux:button icon="trash" variant="ghost" size="sm" />
                                </flux:modal.trigger>

                                <flux:modal name="delete-user-{{ $user->id }}" class="max-w-sm">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                                            <flux:subheading>{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}</flux:subheading>
                                        </div>

                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button type="submit" variant="danger" wire:click="delete({{ $user->id }})">{{ __('Delete') }}</flux:button>
                                        </div>
                                    </div>
                                </flux:modal>--}}
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
