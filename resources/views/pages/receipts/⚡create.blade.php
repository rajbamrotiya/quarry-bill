<?php

use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Flux\Flux;

new #[Title('New Work Receipt')] class extends Component {
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

    // New Client Modal state
    #[Validate('required|string|max:255', as: 'name')]
    public string $new_client_name = '';
    public string $new_client_mobile = '';
    public string $new_client_email = '';

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        $this->time = now()->format('H:i');
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

    #[Computed]
    public function selectedClientName()
    {
        return Client::find($this->client_id)?->name ?? '';
    }

    #[Computed]
    public function selectedMaterialName()
    {
        return MaterialType::find($this->material_type_id)?->name ?? '';
    }

    #[Computed]
    public function nextPassNumber()
    {
        $lastId = Receipt::max('id') ?? 0;
        return str_pad($lastId + 1, 10, '0', STR_PAD_LEFT);
    }

    public function createClient(): void
    {
        $this->validateOnly('new_client_name', [
            'new_client_name' => 'required|string|max:255',
            'new_client_mobile' => 'nullable|string|max:20',
            'new_client_email' => 'nullable|email|max:255',
        ]);

        $client = Client::create([
            'name' => $this->new_client_name,
            'mobile_number' => $this->new_client_mobile,
            'email' => $this->new_client_email,
        ]);

        $this->client_id = (string) $client->id;

        $this->new_client_name = '';
        $this->new_client_mobile = '';
        $this->new_client_email = '';

        $this->resetValidation(['new_client_name', 'new_client_mobile', 'new_client_email']);

        Flux::modal('add-client-modal')->close();
        Flux::toast(__('Client added successfully.'));
    }

    public function preview(): void
    {
        $this->validateOnly('client_id');
        $this->validateOnly('vehicle_number');
        $this->validateOnly('material_type_id');
        $this->validateOnly('royalty_number');
        $this->validateOnly('date');
        $this->validateOnly('time');
        $this->validateOnly('gross_weight');
        $this->validateOnly('tare_weight');
        $this->validateOnly('payment_value');
        $this->validateOnly('payment_type');

        Flux::modal('print-preview-modal')->show();
    }

    public function save(): void
    {
        $this->validateOnly('client_id');
        $this->validateOnly('vehicle_number');
        $this->validateOnly('material_type_id');
        $this->validateOnly('royalty_number');
        $this->validateOnly('date');
        $this->validateOnly('time');
        $this->validateOnly('gross_weight');
        $this->validateOnly('tare_weight');
        $this->validateOnly('payment_value');
        $this->validateOnly('payment_type');
        $this->validateOnly('payment_remark');

        $receipt = Receipt::create([
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

        Flux::toast(__('Receipt generated successfully.'));

        $url = route('receipts.pdf', $receipt);

        $this->js("window.open('$url', '_blank'); window.location.href = '" . route('receipts.index') . "';");
    }
};
?>

<div class="mx-auto max-w-4xl py-6">
    <style>
        .preview-box .receipt-box {
            border: 1.5px solid #ccc;
            padding: 10px 15px;
            box-sizing: border-box;
            background: #fff;
            margin-bottom: 10px;
        }
        .preview-box .office { border-color: #1d4ed8; }
        .preview-box .client { border-color: #047857; }
        .preview-box .transport { border-color: #b45309; }
        .preview-box table { width: 100%; border-collapse: collapse; }
        .preview-box .company-name { font-size: 14px; font-weight: 900; margin: 0; text-transform: uppercase; }
        .preview-box .tagline { font-size: 6px; color: #4b5563; margin: 0; text-transform: uppercase; }
        .preview-box .copy-pill { display: inline-block; padding: 2px 8px; border-radius: 10px; color: #fff; font-weight: bold; text-transform: uppercase; font-size: 7px; }
        .preview-box .office .copy-pill { background: #1d4ed8; }
        .preview-box .client .copy-pill { background: #047857; }
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
        .preview-box .client .net-weight-cell { background: #047857; }
        .preview-box .transport .net-weight-cell { background: #b45309; }
        .preview-box .footer-table td { font-size: 6px; text-transform: uppercase; padding-top: 4px; }
        .preview-box .dot { display: inline-block; width: 4px; height: 4px; border-radius: 50%; margin-right: 3px; }
        .preview-box .office .dot { background: #1d4ed8; }
        .preview-box .client .dot { background: #047857; }
        .preview-box .transport .dot { background: #b45309; }
    </style>

    <div class="mb-8 flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-200">
            <flux:icon name="document-text" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('New Work Receipt') }}</flux:heading>
            <flux:subheading>{{ __('Enter weighing details and link to a client') }}</flux:subheading>
        </div>
    </div>

    <div class="space-y-8">
        {{-- Client Selection Card --}}
        <flux:card class="bg-zinc-50/50 dark:bg-zinc-900/50 border-dashed border-2">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="sm" class="font-bold text-zinc-400 uppercase tracking-widest">{{ __('Client Selection') }}</flux:heading>
                <flux:modal.trigger name="add-client-modal">
                    <flux:button variant="ghost" size="sm" icon="user-plus" class="text-blue-600">
                        {{ __('Add New Client') }}
                    </flux:button>
                </flux:modal.trigger>
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
                <flux:input :value="$this->nextPassNumber" disabled class="bg-zinc-100 font-mono text-zinc-600" />
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

        <div class="flex justify-end pt-4">
            <flux:button wire:click="preview" variant="primary" class="bg-zinc-900 px-10 py-6 rounded-2xl font-bold gap-2">
                <flux:icon name="eye" class="size-5" />
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
                    <x-receipt-slip 
                        :preview="true"
                        :clientName="$this->selectedClientName"
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

                    <x-receipt-slip 
                        :preview="true"
                        :clientName="$this->selectedClientName"
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
                        copyClass="client"
                    />

                    <div class="py-2 border-y border-dashed border-zinc-300 dark:border-zinc-700 text-center text-[10px] text-zinc-400 uppercase tracking-widest font-bold">
                        {{ __('Perforation Line') }}
                    </div>

                    <x-receipt-slip 
                        :preview="true"
                        :clientName="$this->selectedClientName"
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

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Back to Edit') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="save" variant="primary" class="bg-zinc-900 font-bold gap-2">
                    <flux:icon name="check-circle" class="size-5" />
                    {{ __('Confirm & Save') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Add New Client Modal --}}
    <flux:modal name="add-client-modal" class="max-w-md">
        <form wire:submit="createClient" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add New Client') }}</flux:heading>
                <flux:subheading>{{ __('Create a new client to link with this receipt.') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Client Name') }}</flux:label>
                    <flux:input wire:model="new_client_name" :placeholder="__('Enter full name')" />
                    <flux:error name="new_client_name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Mobile Number') }}</flux:label>
                    <flux:input wire:model="new_client_mobile" :placeholder="__('Optional')" />
                    <flux:error name="new_client_mobile" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email Address') }}</flux:label>
                    <flux:input type="email" wire:model="new_client_email" :placeholder="__('Optional')" />
                    <flux:error name="new_client_email" />
                </flux:field>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create Client') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
