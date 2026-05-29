<?php

use App\Models\MaterialType;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Add Material Type')] class extends Component {
    #[Validate('required|string|max:255|unique:material_types,name')]
    public string $name = '';

    #[Validate('nullable|string|max:50')]
    public string $hsn_code = '';

    #[Validate('nullable|numeric|min:0')]
    public $unit_rate = '';

    #[Validate('nullable|string')]
    public string $other_information = '';

    public function save(): void
    {
        $this->validate();

        MaterialType::create([
            'name' => $this->name,
            'hsn_code' => $this->hsn_code,
            'unit_rate' => $this->unit_rate ?: null,
            'other_information' => $this->other_information,
        ]);

        Flux::toast(__('Material type added successfully.'));

        $this->redirect(route('material-types.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-2xl py-6">
    <div class="mb-8 flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200">
            <flux:icon name="tag" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Add Material Type') }}</flux:heading>
            <flux:subheading>{{ __('Create a new category for your quarry materials') }}</flux:subheading>
        </div>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Material Name') }}</flux:label>
                <flux:input wire:model="name" :placeholder="__('e.g. 20 MM (Kapchi)')" autofocus />
                <flux:error name="name" />
            </flux:field>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('HSN Code') }}</flux:label>
                    <flux:input wire:model="hsn_code" :placeholder="__('e.g. 2517')" />
                    <flux:error name="hsn_code" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Unit Rate') }}</flux:label>
                    <flux:input type="number" step="0.01" wire:model="unit_rate" icon="banknotes" :placeholder="__('0.00')" />
                    <flux:error name="unit_rate" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Other Information') }}</flux:label>
                <flux:textarea wire:model="other_information" :placeholder="__('Any additional details...')" />
                <flux:error name="other_information" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button :href="route('material-types.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Save Material') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
