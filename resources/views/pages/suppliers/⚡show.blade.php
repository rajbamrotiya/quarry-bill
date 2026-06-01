<?php

use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Supplier Details')] class extends Component {
    public Supplier $supplier;

    public function mount(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-4xl py-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:button icon="arrow-left" variant="ghost" :href="route('suppliers.index')" wire:navigate />
            <flux:heading size="xl">{{ __('Supplier Details') }}</flux:heading>
        </div>
        <flux:button icon="pencil-square" variant="primary" :href="route('suppliers.edit', $supplier)" wire:navigate>
            {{ __('Edit Supplier') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <flux:card class="lg:col-span-2 space-y-8">
            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Basic Information') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:text class="text-lg font-semibold">{{ $supplier->name }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email Address') }}</flux:label>
                        <flux:text>{{ $supplier->email ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Mobile Number') }}</flux:label>
                        <flux:text>{{ $supplier->mobile_number ?? '-' }}</flux:text>
                    </flux:field>
                </div>
            </section>

            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Location Details') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field class="md:col-span-2">
                        <flux:label>{{ __('Address') }}</flux:label>
                        <flux:text>{{ $supplier->address ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('District') }}</flux:label>
                        <flux:text>{{ $supplier->district ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('State') }}</flux:label>
                        <flux:text>{{ $supplier->state ?? '-' }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Country') }}</flux:label>
                        <flux:text>{{ $supplier->country ?? 'India' }}</flux:text>
                    </flux:field>
                </div>
            </section>

            @if($supplier->other_information)
                <section>
                    <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Other Information') }}</flux:heading>
                    <flux:text>{{ $supplier->other_information }}</flux:text>
                </section>
            @endif
        </flux:card>

        <flux:card class="space-y-8">
            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Tax Information') }}</flux:heading>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('GST Number') }}</flux:label>
                        <flux:badge variant="neutral" class="font-mono text-sm uppercase">{{ $supplier->gst_number ?? __('Not provided') }}</flux:badge>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('PAN Number') }}</flux:label>
                        <flux:badge variant="neutral" class="font-mono text-sm uppercase">{{ $supplier->pan_number ?? __('Not provided') }}</flux:badge>
                    </flux:field>
                </div>
            </section>

            <section>
                <flux:heading size="lg" class="mb-4 border-b pb-2">{{ __('Metadata') }}</flux:heading>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Created At') }}</flux:label>
                        <flux:text class="text-sm">{{ $supplier->created_at->format('M d, Y H:i') }}</flux:text>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Last Updated') }}</flux:label>
                        <flux:text class="text-sm">{{ $supplier->updated_at->format('M d, Y H:i') }}</flux:text>
                    </flux:field>
                </div>
            </section>
        </flux:card>
    </div>
</div>
