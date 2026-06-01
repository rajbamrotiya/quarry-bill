<?php

use App\Models\Supplier;
use App\Support\Location;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Create Supplier')] class extends Component {
    #[Validate('required|string|max:255|unique:suppliers,name')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public string $mobile_number = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string')]
    public string $address = '';

    #[Validate('nullable|string|size:15')]
    public string $gst_number = '';

    #[Validate('nullable|string|size:10')]
    public string $pan_number = '';

    #[Validate('required|string')]
    public string $country = 'India';

    #[Validate('required|string')]
    public string $state = 'Gujarat';

    #[Validate('required|string')]
    public string $district = '';

    #[Validate('nullable|string')]
    public string $other_information = '';

    public function states(): array
    {
        return Location::states();
    }

    public function districts(): array
    {
        if ($this->state === 'Gujarat') {
            return Location::gujaratDistricts();
        }

        return [];
    }

    public function save(): void
    {
        $this->validate();

        Supplier::create([
            'name' => $this->name,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
            'address' => $this->address,
            'gst_number' => $this->gst_number,
            'pan_number' => $this->pan_number,
            'country' => $this->country,
            'state' => $this->state,
            'district' => $this->district,
            'other_information' => $this->other_information,
        ]);

        Flux::toast(__('Supplier created successfully.'));

        $this->redirect(route('suppliers.index'), navigate: true);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 max-w-2xl py-6">
    <div class="flex items-center gap-2">
        <flux:button icon="arrow-left" variant="ghost" :href="route('suppliers.index')" wire:navigate />
        <flux:heading size="xl">{{ __('Create Supplier') }}</flux:heading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field class="md:col-span-2">
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="name" :placeholder="__('Enter supplier name')" autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Mobile Number') }}</flux:label>
                    <flux:input wire:model="mobile_number" :placeholder="__('Enter mobile number')" />
                    <flux:error name="mobile_number" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email Address') }}</flux:label>
                    <flux:input type="email" wire:model="email" :placeholder="__('Enter email address')" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>{{ __('Address') }}</flux:label>
                    <flux:textarea wire:model="address" :placeholder="__('Enter supplier address')" />
                    <flux:error name="address" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('GST Number') }}</flux:label>
                    <flux:input wire:model="gst_number" :placeholder="__('15-digit GSTIN')" maxlength="15" />
                    <flux:error name="gst_number" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('PAN Number') }}</flux:label>
                    <flux:input wire:model="pan_number" :placeholder="__('10-digit PAN')" maxlength="10" />
                    <flux:error name="pan_number" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Country') }}</flux:label>
                    <flux:input wire:model="country" readonly />
                    <flux:error name="country" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('State') }}</flux:label>
                    <flux:select wire:model.live="state">
                        @foreach($this->states() as $stateName)
                            <flux:select.option :value="$stateName">{{ $stateName }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="state" />
                </flux:field>

                @if($state === 'Gujarat')
                    <flux:field>
                        <flux:label>{{ __('District') }}</flux:label>
                        <flux:select wire:model="district" :placeholder="__('Select district')">
                            @foreach($this->districts() as $districtName)
                                <flux:select.option :value="$districtName">{{ $districtName }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="district" />
                    </flux:field>
                @else
                    <flux:field>
                        <flux:label>{{ __('District') }}</flux:label>
                        <flux:input wire:model="district" :placeholder="__('Enter district name')" />
                        <flux:error name="district" />
                    </flux:field>
                @endif
            </div>

            <flux:field>
                <flux:label>{{ __('Other Information') }}</flux:label>
                <flux:textarea wire:model="other_information" :placeholder="__('Any additional details...')" />
                <flux:error name="other_information" />
            </flux:field>

            <div class="flex gap-2 pt-4">
                <flux:spacer />
                <flux:button :href="route('suppliers.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Save Supplier') }}</flux:button>
            </div>
        </form>
    </flux:card>
</div>
