<?php

use App\Models\Supplier;
use App\Models\MaterialType;
use App\Models\BuyReceipt;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('Edit BuyReceipt')] class extends Component {
    public BuyReceipt $buy_receipt;

    #[Validate('required|exists:suppliers,id')]
    public string $supplier_id = '';

    #[Validate('required|string|max:20')]
    public string $vehicle_number = '';

    #[Validate('required|exists:material_types,id')]
    public string $material_type_id = '';

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('required')]
    public string $time = '';

    #[Validate('required|numeric|min:0')]
    public int $gross_weight = 0;

    #[Validate('required|numeric|min:0|lt:gross_weight')]
    public int $tare_weight = 0;

    public int $net_weight = 0;

    #[Validate('nullable|string|max:255')]
    public string $royalty_number = '';

    public string $remarks = '';

    public function mount(BuyReceipt $buy_receipt): void
    {
        $this->buy_receipt = $buy_receipt;
        $this->supplier_id = (string) $buy_receipt->supplier_id;
        $this->vehicle_number = $buy_receipt->vehicle_number;
        $this->material_type_id = (string) $buy_receipt->material_type_id;
        $this->royalty_number = $buy_receipt->royalty_number ?? '';
        $this->date = $buy_receipt->date->format('Y-m-d');
        $this->time = $buy_receipt->time;
        $this->gross_weight = (int) $buy_receipt->gross_weight;
        $this->tare_weight = (int) $buy_receipt->tare_weight;
        $this->payment_value = $buy_receipt->payment_value ? (float) $buy_receipt->payment_value : null;
        $this->payment_type = $buy_receipt->payment_type ?? '';
        $this->payment_remark = $buy_receipt->payment_remark ?? '';
        $this->remarks = $buy_receipt->remarks ?? '';
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
        $this->net_weight = max(0, (int)$this->gross_weight - (int)$this->tare_weight);
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    #[Computed]
    public function materialTypes()
    {
        return MaterialType::orderBy('name')->get();
    }

    public function save(): void
    {
        $this->validate();

        $this->buy_receipt->update([
            'supplier_id' => $this->supplier_id,
            'vehicle_number' => $this->vehicle_number,
            'material_type_id' => $this->material_type_id,
            'royalty_number' => $this->royalty_number,
            'date' => $this->date,
            'time' => $this->time,
            'gross_weight' => $this->gross_weight,
            'tare_weight' => $this->tare_weight,
            'remarks' => $this->remarks,
        ]);

        Flux::toast(__('BuyReceipt updated successfully.'));

        $this->redirect(route('buy-receipts.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-4xl py-6">
    <div class="mb-8 flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-orange-600 text-white shadow-lg shadow-orange-200">
            <flux:icon name="pencil-square" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Edit Work BuyReceipt') }}</flux:heading>
            <flux:subheading>{{ __('Modify weighing details for pass #') }}{{ $buy_receipt->id }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        {{-- Supplier Selection Card --}}
        <flux:card class="bg-zinc-50/50 dark:bg-zinc-900/50 border-dashed border-2">
            <div class="mb-4">
                <flux:heading size="sm" class="font-bold text-zinc-400 uppercase tracking-widest">{{ __('Supplier Selection') }}</flux:heading>
            </div>

            <livewire:autocomplete 
                wire:model="supplier_id" 
                :model="\App\Models\Supplier::class" 
                :placeholder="__('-- Choose a supplier --')" 
                :label="__('Select Supplier')" 
            />

            <flux:error name="supplier_id" />
        </flux:card>

        {{-- Main Form Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:field>
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Pass Number') }}</flux:label>
                <flux:input :value="$buy_receipt->pass_number" disabled class="bg-zinc-100 font-mono text-zinc-600" />
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
                let val = (parseInt(this.gross, 10) || 0) - (parseInt(this.tare, 10) || 0);
                return val > 0 ? val.toString() : '0';
            }
        }">
            <flux:card class="flex flex-col items-center justify-center py-8 bg-zinc-50/30">
                <flux:heading size="sm" class="uppercase text-[10px] font-bold text-zinc-400 mb-4 tracking-tight">{{ __('Gross Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <input type="number" step="1" x-model="gross" class="w-full text-center text-3xl font-black bg-transparent border-none focus:ring-0" />
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ __('KG') }}</span>
                </div>
                <flux:error name="gross_weight" />
            </flux:card>

            <flux:card class="flex flex-col items-center justify-center py-8 bg-zinc-50/30">
                <flux:heading size="sm" class="uppercase text-[10px] font-bold text-zinc-400 mb-4 tracking-tight">{{ __('Tare Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <input type="number" step="1" x-model="tare" class="w-full text-center text-3xl font-black bg-transparent border-none focus:ring-0" />
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ __('KG') }}</span>
                </div>
                <flux:error name="tare_weight" />
            </flux:card>

            <div class="flex flex-col items-center justify-center py-8 rounded-2xl bg-blue-600 text-white shadow-xl shadow-blue-200">
                <flux:heading size="sm" class="uppercase text-[10px] font-bold text-blue-100 mb-4 tracking-tight">{{ __('Net Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <span class="text-4xl font-black" x-text="net"></span>
                    <span class="text-[10px] font-bold text-blue-100 uppercase tracking-widest mt-1">{{ __('KG') }}</span>
                    <span class="text-[9px] font-medium text-blue-200 mt-2 uppercase tracking-tighter">{{ __('Total Calculated') }}</span>
                </div>
            </div>
        </div>



        <flux:field>
            <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-1 tracking-tight">{{ __('Remarks (Optional)') }}</flux:label>
            <flux:textarea wire:model="remarks" :placeholder="__('Any additional notes...')" />
            <flux:error name="remarks" />
        </flux:field>

        <div class="flex justify-end pt-4 gap-4">
            <flux:button :href="route('buy-receipts.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="bg-zinc-900 px-10 py-6 rounded-2xl font-bold gap-2">
                <flux:icon name="check" class="size-5" />
                {{ __('Update Work Slip') }}
            </flux:button>
        </div>
    </form>
</div>
