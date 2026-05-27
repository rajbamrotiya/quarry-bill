<?php

use App\Models\MaterialType;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Edit Material Type')] class extends Component {
    public MaterialType $materialType;

    #[Validate]
    public string $name = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:material_types,name,' . $this->materialType->id,
        ];
    }

    public function mount(MaterialType $materialType): void
    {
        $this->materialType = $materialType;
        $this->name = $materialType->name;
    }

    public function save(): void
    {
        $this->validate();

        $this->materialType->update([
            'name' => $this->name,
        ]);

        Flux::toast(__('Material type updated successfully.'));

        $this->redirect(route('material-types.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-2xl py-6">
    <div class="mb-8 flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-orange-600 text-white shadow-lg shadow-orange-200">
            <flux:icon name="pencil-square" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Edit Material Type') }}</flux:heading>
            <flux:subheading>{{ __('Modify the name or category of this material') }}</flux:subheading>
        </div>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Material Name') }}</flux:label>
                <flux:input wire:model="name" autofocus />
                <flux:error name="name" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button :href="route('material-types.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" class="bg-zinc-900">{{ __('Update Material') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
