<?php

use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Edit Receipt')] class extends Component {
    public Receipt $receipt;

    #[Validate('required|exists:clients,id')]
    public string $client_id = '';

    #[Validate('required|string|max:20')]
    public string $vehicle_number = '';

    #[Validate('required|exists:material_types,id')]
    public string $material_type_id = '';

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required')]
    public string $time = '';

    #[Validate('required|numeric|min:0')]
    public float $gross_weight = 0;

    #[Validate('required|numeric|min:0|lt:gross_weight')]
    public float $tare_weight = 0;

    public float $net_weight = 0;

    #[Validate('nullable|string|max:255')]
    public string $royalty_number = '';

    #[Validate('nullable|numeric|min:0')]
    public ?float $payment_value = null;

    #[Validate('nullable|in:cash,online')]
    public string $payment_type = '';

    #[Validate('nullable|string|max:255')]
    public string $payment_remark = '';

    public string $remarks = '';

    public function mount(Receipt $receipt): void
    {
        $this->receipt = $receipt;
        $this->client_id = (string) $receipt->client_id;
        $this->vehicle_number = $receipt->vehicle_number;
        $this->material_type_id = (string) $receipt->material_type_id;
        $this->royalty_number = $receipt->royalty_number ?? '';
        $this->date = $receipt->date->format('Y-m-d');
        $this->time = $receipt->time;
        $this->gross_weight = (float) $receipt->gross_weight;
        $this->tare_weight = (float) $receipt->tare_weight;
        $this->payment_value = $receipt->payment_value ? (float) $receipt->payment_value : null;
        $this->payment_type = $receipt->payment_type ?? '';
        $this->payment_remark = $receipt->payment_remark ?? '';
        $this->remarks = $receipt->remarks ?? '';
        $this->calculateNetWeight();
    }

    public function updatedGrossWeight(): void
    {
        $this->validateOnly('gross_weight');
        $this->validateOnly('tare_weight');
        $this->calculateNetWeight();
    }

    public function updatedTareWeight(): void
    {
        $this->validateOnly('tare_weight');
        $this->calculateNetWeight();
    }

    protected function calculateNetWeight(): void
    {
        $this->net_weight = max(0, (float)$this->gross_weight - (float)$this->tare_weight);
    }

    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get();
    }

    #[Computed]
    public function materialTypes()
    {
        return MaterialType::orderBy('name')->get();
    }

    public function save(): void
    {
        $this->validate();

        $this->receipt->update([
            'client_id' => $this->client_id,
            'vehicle_number' => $this->vehicle_number,
            'material_type_id' => $this->material_type_id,
            'royalty_number' => $this->royalty_number,
            'date' => $this->date,
            'time' => $this->time,
            'gross_weight' => $this->gross_weight,
            'tare_weight' => $this->tare_weight,
            'remarks' => $this->remarks,
            'payment_value' => $this->payment_value,
            'payment_type' => $this->payment_type,
            'payment_remark' => $this->payment_remark,
        ]);

        Flux::toast(__('Receipt updated successfully.'));

        $this->redirect(route('receipts.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-4xl py-6">
    <div class="mb-8 flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-orange-600 text-white shadow-lg shadow-orange-200">
            <flux:icon name="pencil-square" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Edit Work Receipt') }}</flux:heading>
            <flux:subheading>{{ __('Modify weighing details for pass #') }}{{ $receipt->id }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        {{-- Client Selection Card --}}
        <flux:card class="bg-zinc-50/50 dark:bg-zinc-900/50 border-dashed border-2">
            <div class="mb-4">
                <flux:heading size="sm" class="font-bold text-zinc-400 uppercase tracking-widest">{{ __('Client Selection') }}</flux:heading>
            </div>

            <livewire:autocomplete 
                wire:model="client_id" 
                :model="\App\Models\Client::class" 
                :placeholder="__('-- Choose a client --')" 
                :label="__('Select Client')" 
            />

            <flux:error name="client_id" />
        </flux:card>

        {{-- Main Form Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Pass Number') }}</flux:label>
                <flux:input :value="str_pad($receipt->id, 10, '0', STR_PAD_LEFT)" disabled class="bg-zinc-100 font-mono text-zinc-600" />
            </flux:field>

            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Vehicle Number') }}</flux:label>
                <flux:input wire:model="vehicle_number" :placeholder="__('e.g. GJ-01-XX-0000')" />
                <flux:error name="vehicle_number" />
            </flux:field>

            <livewire:autocomplete 
                wire:model="material_type_id" 
                :model="\App\Models\MaterialType::class" 
                :placeholder="__('Select Material')" 
                :label="__('Material Type')" 
            />
            <flux:error name="material_type_id" />

            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Royalty Number') }}</flux:label>
                <flux:input wire:model="royalty_number" :placeholder="__('Optional')" />
                <flux:error name="royalty_number" />
            </flux:field>

            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Date') }}</flux:label>
                <flux:input type="date" wire:model="date" icon="calendar" />
                <flux:error name="date" />
            </flux:field>

            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Time') }}</flux:label>
                <flux:input type="time" wire:model="time" icon="clock" />
                <flux:error name="time" />
            </flux:field>
        </div>

        {{-- Weight Section --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ 
            gross: @entangle('gross_weight').live, 
            tare: @entangle('tare_weight').live,
            get net() { 
                let val = (parseFloat(this.gross) || 0) - (parseFloat(this.tare) || 0);
                return val > 0 ? val.toFixed(3) : '0.000';
            }
        }">
            <flux:card class="flex flex-col items-center justify-center py-8 bg-zinc-50/30">
                <flux:heading size="sm" class="uppercase text-[10px] font-bold text-zinc-400 mb-4 tracking-tight">{{ __('Gross Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <input type="number" step="0.001" x-model="gross" class="w-full text-center text-3xl font-black bg-transparent border-none focus:ring-0" />
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ __('Tons') }}</span>
                </div>
                <flux:error name="gross_weight" />
            </flux:card>

            <flux:card class="flex flex-col items-center justify-center py-8 bg-zinc-50/30">
                <flux:heading size="sm" class="uppercase text-[10px] font-bold text-zinc-400 mb-4 tracking-tight">{{ __('Tare Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <input type="number" step="0.001" x-model="tare" class="w-full text-center text-3xl font-black bg-transparent border-none focus:ring-0" />
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ __('Tons') }}</span>
                </div>
                <flux:error name="tare_weight" />
            </flux:card>

            <div class="flex flex-col items-center justify-center py-8 rounded-2xl bg-blue-600 text-white shadow-xl shadow-blue-200">
                <flux:heading size="sm" class="uppercase text-[10px] font-bold text-blue-100 mb-4 tracking-tight">{{ __('Net Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <span class="text-4xl font-black" x-text="net"></span>
                    <span class="text-[10px] font-bold text-blue-100 uppercase tracking-widest mt-1">{{ __('Tons') }}</span>
                    <span class="text-[9px] font-medium text-blue-200 mt-2 uppercase tracking-tighter">{{ __('Total Calculated') }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Section --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Payment Value') }}</flux:label>
                <flux:input type="number" step="0.01" wire:model="payment_value" :placeholder="__('0.00')" icon="currency-dollar" />
                <flux:error name="payment_value" />
            </flux:field>

            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Payment Type') }}</flux:label>
                <flux:select wire:model="payment_type" :placeholder="__('Select Payment Type')">
                    <flux:select.option value="cash">{{ __('Cash') }}</flux:select.option>
                    <flux:select.option value="online">{{ __('Online') }}</flux:select.option>
                </flux:select>
                <flux:error name="payment_type" />
            </flux:field>

            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Payment Remark') }}</flux:label>
                <flux:input wire:model="payment_remark" :placeholder="__('e.g. Paid by GPay')" list="payment-remarks-list" />
                <datalist id="payment-remarks-list">
                    @foreach(\App\Models\Receipt::whereNotNull('payment_remark')->distinct()->pluck('payment_remark') as $suggestion)
                        <option value="{{ $suggestion }}">
                    @endforeach
                </datalist>
                <flux:error name="payment_remark" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Remarks (Optional)') }}</flux:label>
            <flux:textarea wire:model="remarks" :placeholder="__('Any additional notes...')" />
            <flux:error name="remarks" />
        </flux:field>

        <div class="flex justify-end pt-4 gap-4">
            <flux:button :href="route('receipts.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="bg-zinc-900 px-10 py-6 rounded-2xl font-bold gap-2">
                <flux:icon name="check" class="size-5" />
                {{ __('Update Work Slip') }}
            </flux:button>
        </div>
    </form>
</div>
