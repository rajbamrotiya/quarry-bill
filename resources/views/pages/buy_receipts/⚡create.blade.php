<?php

use App\Models\Supplier;
use App\Models\MaterialType;
use App\Models\BuyReceipt;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('New Work BuyReceipt')] class extends Component {
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

    // New Supplier Modal state
    #[Validate('required|string|max:255', as: 'name')]
    public string $new_supplier_name = '';
    public string $new_supplier_mobile = '';
    public string $new_supplier_email = '';

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');
        $this->calculateNetWeight();

        $lastReceipt = BuyReceipt::latest()->first();
        if ($lastReceipt) {
            $this->material_type_id = (string) $lastReceipt->material_type_id;
        }
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

    public function fetchTareWeight(): void
    {
        if (blank($this->vehicle_number)) {
            return;
        }

        $lastReceipt = BuyReceipt::where('vehicle_number', $this->vehicle_number)
            ->latest('id')
            ->first();

        if ($lastReceipt && $lastReceipt->tare_weight > 0) {
            $this->tare_weight = $lastReceipt->tare_weight;
            $this->calculateNetWeight();
        }
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

    #[Computed]
    public function selectedSupplierName()
    {
        return Supplier::find($this->supplier_id)?->name ?? '';
    }

    #[Computed]
    public function selectedMaterialName()
    {
        return MaterialType::find($this->material_type_id)?->name ?? '';
    }

    #[Computed]
    public function nextPassNumber()
    {
        $date = $this->date ? \Carbon\Carbon::parse($this->date) : now();
        $prefix = 'BUY/' . $date->format('Y/m/');

        $lastBuyReceipt = BuyReceipt::where('pass_number', 'like', $prefix.'%')
            ->orderBy('pass_number', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastBuyReceipt) {
            $lastNumber = (int) substr($lastBuyReceipt->pass_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function createSupplier(): void
    {
        $this->validateOnly('new_supplier_name', [
            'new_supplier_name' => 'required|string|max:255|unique:suppliers,name',
            'new_supplier_mobile' => 'nullable|string|max:20',
            'new_supplier_email' => 'nullable|email|max:255',
        ]);

        $supplier = Supplier::create([
            'name' => $this->new_supplier_name,
            'mobile_number' => $this->new_supplier_mobile,
            'email' => $this->new_supplier_email,
        ]);

        $this->supplier_id = (string) $supplier->id;

        $this->new_supplier_name = '';
        $this->new_supplier_mobile = '';
        $this->new_supplier_email = '';

        $this->resetValidation(['new_supplier_name', 'new_supplier_mobile', 'new_supplier_email']);

        Flux::modal('add-supplier-modal')->close();
        Flux::toast(__('Supplier added successfully.'));
    }

    public function preview(): void
    {
        $this->validateOnly('supplier_id');
        $this->validateOnly('vehicle_number');
        $this->validateOnly('material_type_id');
        $this->validateOnly('royalty_number');
        $this->validateOnly('date');
        $this->validateOnly('time');
        $this->validateOnly('gross_weight');
        $this->validateOnly('tare_weight');

        Flux::modal('print-preview-modal')->show();
    }

    public function save(bool $print = true): void
    {
        $this->validateOnly('supplier_id');
        $this->validateOnly('vehicle_number');
        $this->validateOnly('material_type_id');
        $this->validateOnly('royalty_number');
        $this->validateOnly('date');
        $this->validateOnly('time');
        $this->validateOnly('gross_weight');
        $this->validateOnly('tare_weight');

        $buy_receipt = BuyReceipt::create([
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

        Flux::toast(__('Buy Receipt generated successfully.'));

        if ($print) {
            $url = route('buy-receipts.pdf', $buy_receipt);
            $this->js("window.open('$url', '_blank'); window.location.href = '" . route('buy-receipts.index') . "';");
        } else {
            $this->redirect(route('buy-receipts.index'), navigate: true);
        }
    }
};
?>

<div class="mx-auto max-w-4xl py-6">
    <style>
        .preview-box .buy_receipt-box {
            border: 1.5px solid #ccc;
            padding: 10px 15px;
            box-sizing: border-box;
            background: #fff;
            margin-bottom: 10px;
        }
        .preview-box .office { border-color: #1d4ed8; }
        .preview-box .supplier { border-color: #047857; }
        .preview-box .transport { border-color: #b45309; }
        .preview-box table { width: 100%; border-collapse: collapse; }
        .preview-box .company-name { font-size: 14px; font-weight: 900; margin: 0; text-transform: uppercase; }
        .preview-box .tagline { font-size: 6px; color: #4b5563; margin: 0; text-transform: uppercase; }
        .preview-box .copy-pill { display: inline-block; padding: 2px 8px; border-radius: 10px; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 7px; }
        .preview-box .office .copy-pill { background: #1d4ed8; }
        .preview-box .supplier .copy-pill { background: #047857; }
        .preview-box .transport .copy-pill { background: #b45309; }
        .preview-box .slip-value { font-size: 10px; font-weight: 900; margin: 0; }
        .preview-box .field-label { font-size: 6px; color: #9ca3af; text-transform: uppercase; margin: 0; }
        .preview-box .field-value { font-size: 9px; font-weight: 800; text-transform: uppercase; margin: 0; }
        .preview-box .weight-table { margin-top: 5px; }
        .preview-box .weight-table td { border: 1px solid #111827; padding: 5px; text-align: center; }
        .preview-box .weight-label { font-size: 6px; color: #4b5563; text-transform: uppercase; margin-bottom: 2px; }
        .preview-box .weight-value { font-size: 12px; font-weight: 900; margin: 0; }
        .preview-box .net-weight-cell { color: #fff; border: none !important; }
        .preview-box .office .net-weight-cell { background: #1d4ed8; }
        .preview-box .supplier .net-weight-cell { background: #047857; }
        .preview-box .transport .net-weight-cell { background: #b45309; }
        .preview-box .footer-table td { font-size: 6px; text-transform: uppercase; padding-top: 4px; }
        .preview-box .dot { display: inline-block; width: 4px; height: 4px; border-radius: 50%; margin-right: 3px; }
        .preview-box .office .dot { background: #1d4ed8; }
        .preview-box .supplier .dot { background: #047857; }
        .preview-box .transport .dot { background: #b45309; }
    </style>

    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="flex size-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200">
                <flux:icon name="document-text" class="size-6" variant="outline" />
            </div>
            <div>
                <flux:heading size="xl" class="font-bold">{{ __('New Buy Receipt') }}</flux:heading>
                <flux:subheading>{{ __('Enter weighing details and link to a supplier') }}</flux:subheading>
            </div>
        </div>

        {{-- Highlighted Pass Number --}}
        <div class="flex flex-col items-end">
            <span class="uppercase text-[10px] font-bold text-zinc-400 tracking-widest">{{ __('Pass Number') }}</span>
            <div class="bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500 font-mono text-2xl font-black px-5 py-2 rounded-xl border border-amber-200 dark:border-amber-800 shadow-md mt-1">
                {{ $this->nextPassNumber }}
            </div>
        </div>
    </div>

    <div class="space-y-8">
        {{-- Date and Time Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label class="uppercase text-sm font-bold text-sky-500 mb-1 tracking-tight">{{ __('Date') }}</flux:label>
                <flux:input type="date" wire:model="date" icon="calendar" class="bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100" />
                <flux:error name="date" />
            </flux:field>

            <flux:field>
                <flux:label class="uppercase text-sm font-bold text-violet-500 mb-1 tracking-tight">{{ __('Time') }}</flux:label>
                <flux:input type="time" wire:model="time" icon="clock" class="bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100" />
                <flux:error name="time" />
            </flux:field>
        </div>

        {{-- Supplier Selection Card --}}
        <flux:card class="bg-blue-50/50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 border-2">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="sm" class="font-bold text-zinc-400 uppercase tracking-widest">{{ __('Supplier Selection') }}</flux:heading>
                <flux:modal.trigger name="add-supplier-modal">
                    <flux:button variant="ghost" size="sm" icon="user-plus" class="text-blue-600">
                        {{ __('Add New Supplier') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>

            <livewire:autocomplete 
                wire:model="supplier_id" 
                :model="\App\Models\Supplier::class" 
                :placeholder="__('-- Choose a supplier --')" 
                :label="__('Select Supplier')" 
                :labelClass="'text-teal-500'"
            />

            <flux:error name="supplier_id" />
        </flux:card>

        {{-- Main Form Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label class="uppercase text-sm font-bold text-amber-500 mb-1 tracking-tight">{{ __('Vehicle Number') }}</flux:label>
                <flux:input wire:model="vehicle_number" wire:change="fetchTareWeight" :placeholder="__('e.g. GJ-01-XX-0000')" list="buy-vehicle-numbers-list" class="bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100" />
                <datalist id="buy-vehicle-numbers-list">
                    @foreach(\App\Models\BuyReceipt::whereNotNull('vehicle_number')->distinct()->pluck('vehicle_number') as $suggestion)
                        <option value="{{ $suggestion }}">
                    @endforeach
                </datalist>
                <flux:error name="vehicle_number" />
            </flux:field>

            <livewire:autocomplete 
                wire:model="material_type_id" 
                :model="\App\Models\MaterialType::class" 
                :placeholder="__('Select Material')" 
                :label="__('Material Type')" 
                :labelClass="'text-rose-500'"
                class="bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100"
            />
            <flux:error name="material_type_id" />

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
            <flux:card class="flex flex-col items-center justify-center py-8 bg-indigo-50/50 dark:bg-indigo-900/30 border-indigo-200 dark:border-indigo-800">
                <flux:heading size="sm" class="uppercase text-sm font-bold text-indigo-500 dark:text-indigo-400 mb-4 tracking-tight">{{ __('Gross Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <input type="number" step="1" x-model="gross" class="w-full text-center text-3xl font-black bg-transparent border-none focus:ring-0 text-indigo-900 dark:text-indigo-100" />
                    <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">{{ __('KG') }}</span>
                </div>
                <flux:error name="gross_weight" />
            </flux:card>

            <flux:card class="flex flex-col items-center justify-center py-8 bg-indigo-50/50 dark:bg-indigo-900/30 border-indigo-200 dark:border-indigo-800">
                <flux:heading size="sm" class="uppercase text-sm font-bold text-indigo-500 dark:text-indigo-400 mb-4 tracking-tight">{{ __('Tare Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <input type="number" step="1" x-model="tare" class="w-full text-center text-3xl font-black bg-transparent border-none focus:ring-0 text-indigo-900 dark:text-indigo-100" />
                    <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">{{ __('KG') }}</span>
                </div>
                <flux:error name="tare_weight" />
            </flux:card>

            <div class="flex flex-col items-center justify-center py-8 rounded-2xl bg-blue-600 text-white shadow-xl shadow-blue-200">
                <flux:heading size="sm" class="uppercase text-sm font-bold text-blue-100 mb-4 tracking-tight">{{ __('Net Weight') }}</flux:heading>
                <div class="flex flex-col items-center">
                    <span class="text-4xl font-black" x-text="net"></span>
                    <span class="text-[10px] font-bold text-blue-100 uppercase tracking-widest mt-1">{{ __('KG') }}</span>
                    <span class="text-[9px] font-medium text-blue-200 mt-2 uppercase tracking-tighter">{{ __('Total Calculated') }}</span>
                </div>
            </div>
        </div>



        <flux:field>
            <flux:label class="uppercase text-sm font-bold text-pink-500 mb-1 tracking-tight">{{ __('Royalty Number') }}</flux:label>
            <flux:input wire:model="royalty_number" :placeholder="__('Optional')" class="bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100" />
            <flux:error name="royalty_number" />
        </flux:field>

        <flux:field>
            <flux:label class="uppercase text-sm font-bold text-gray-500 mb-1 tracking-tight">{{ __('Remarks (Optional)') }}</flux:label>
            <flux:textarea wire:model="remarks" :placeholder="__('Any additional notes...')" class="bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100" />
            <flux:error name="remarks" />
        </flux:field>

        <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-800">
            <flux:button wire:click="preview" variant="primary" icon="eye" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400 shadow-md text-white font-bold py-3 text-base">
                {{ __('Preview Work Slip') }}
            </flux:button>
        </div>
    </div>

    {{-- Print Preview Modal --}}
    <flux:modal name="print-preview-modal" class="max-w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Print Preview') }}</flux:heading>
                <flux:subheading>{{ __('Please review the details before saving and printing.') }}</flux:subheading>
            </div>

            <div class="preview-box p-6 bg-zinc-100 dark:bg-zinc-800/50 rounded-2xl max-h-[700px] overflow-y-auto shadow-inner">
                <div class="space-y-4">
                    <x-buy_receipt-slip 
                        :preview="true"
                        :supplierName="$this->selectedSupplierName"
                        :vehicleNumber="$vehicle_number"
                        :materialName="$this->selectedMaterialName"
                        :date="$date"
                        :time="$time"
                        :gross="$gross_weight"
                        :tare="$tare_weight"
                        :net="$net_weight"
                        :remarks="$remarks"
                        :slipNumber="'#' . $this->nextPassNumber"
                        copyType="OFFICE COPY"
                        copyClass="office"
                    />

                    <div class="py-2 border-y border-dashed border-zinc-300 dark:border-zinc-700 text-center text-[10px] text-zinc-400 uppercase tracking-widest font-bold">
                        {{ __('Perforation Line') }}
                    </div>

                    <x-buy_receipt-slip 
                        :preview="true"
                        :supplierName="$this->selectedSupplierName"
                        :vehicleNumber="$vehicle_number"
                        :materialName="$this->selectedMaterialName"
                        :date="$date"
                        :time="$time"
                        :gross="$gross_weight"
                        :tare="$tare_weight"
                        :net="$net_weight"
                        :remarks="$remarks"
                        :slipNumber="'#' . $this->nextPassNumber"
                        copyType="CLIENT COPY"
                        copyClass="supplier"
                    />

                    <div class="py-2 border-y border-dashed border-zinc-300 dark:border-zinc-700 text-center text-[10px] text-zinc-400 uppercase tracking-widest font-bold">
                        {{ __('Perforation Line') }}
                    </div>

                    <x-buy_receipt-slip 
                        :preview="true"
                        :supplierName="$this->selectedSupplierName"
                        :vehicleNumber="$vehicle_number"
                        :materialName="$this->selectedMaterialName"
                        :date="$date"
                        :time="$time"
                        :gross="$gross_weight"
                        :tare="$tare_weight"
                        :net="$net_weight"
                        :remarks="$remarks"
                        :slipNumber="'#' . $this->nextPassNumber"
                        copyType="TRANSPORT COPY"
                        copyClass="transport"
                    />
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4 mt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:spacer class="hidden sm:block" />
                <flux:modal.close>
                    <flux:button variant="ghost" class="w-full sm:w-auto">{{ __('Back to Edit') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save(false)" variant="outline" icon="check" class="w-full sm:w-auto">
                    {{ __('Save Only') }}
                </flux:button>
                <flux:button wire:click="save(true)" variant="primary" icon="printer" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-400 shadow-md text-white font-bold">
                    {{ __('Save & Print') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Add New Supplier Modal --}}
    <flux:modal name="add-supplier-modal" class="max-w-md">
        <form wire:submit="createSupplier" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add New Supplier') }}</flux:heading>
                <flux:subheading>{{ __('Create a new supplier to link with this buy receipt.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Supplier Name') }}</flux:label>
                    <flux:input wire:model="new_supplier_name" :placeholder="__('Enter full name')" />
                    <flux:error name="new_supplier_name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Mobile Number') }}</flux:label>
                    <flux:input wire:model="new_supplier_mobile" :placeholder="__('Optional')" />
                    <flux:error name="new_supplier_mobile" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email Address') }}</flux:label>
                    <flux:input type="email" wire:model="new_supplier_email" :placeholder="__('Optional')" />
                    <flux:error name="new_supplier_email" />
                </flux:field>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create Supplier') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Full Page Loader for Fetching Tare Weight --}}
    <div wire:loading wire:target="fetchTareWeight" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/20 backdrop-blur-sm">
        <div class="bg-white dark:bg-zinc-800 p-5 rounded-2xl shadow-2xl flex flex-col items-center gap-3">
            <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-bold text-zinc-700 dark:text-zinc-200">{{ __('Fetching vehicle details...') }}</span>
        </div>
    </div>
</div>
