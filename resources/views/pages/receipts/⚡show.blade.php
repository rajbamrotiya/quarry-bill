<?php

use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Receipt Details')] class extends Component {
    public Receipt $receipt;

    public function mount(Receipt $receipt): void
    {
        $this->receipt = $receipt->load(['client', 'materialType']);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-4xl mx-auto py-6">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b pb-6">
        <div class="flex items-center gap-4">
            <flux:button icon="arrow-left" variant="ghost" :href="route('receipts.index')" wire:navigate />
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-black">{{ __('Receipt Details') }}</flux:heading>
                    <flux:badge color="indigo" size="sm" inset="top" class="font-mono">#{{ $receipt->pass_number ?: str_pad($receipt->id, 10, '0', STR_PAD_LEFT) }}</flux:badge>
                </div>
                <flux:subheading>{{ __('Detailed information for dispatch pass') }}</flux:subheading>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <flux:button icon="arrow-down-tray" variant="ghost" :href="route('receipts.pdf', $receipt)" target="_blank">
                {{ __('Download PDF') }}
            </flux:button>
            <flux:button icon="pencil-square" variant="primary" :href="route('receipts.edit', $receipt)" wire:navigate class="bg-indigo-600 border-indigo-600">
                {{ __('Edit Receipt') }}
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Core Info --}}
        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <div class="flex items-center gap-2 mb-6 border-b pb-4">
                    <flux:icon name="identification" class="size-5 text-zinc-400" />
                    <flux:heading size="lg">{{ __('Dispatch Information') }}</flux:heading>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Consignee / Client') }}</flux:label>
                        <div class="flex flex-col">
                            <flux:text class="text-lg font-bold text-zinc-900 dark:text-white">{{ $receipt->client->name }}</flux:text>
                            @if($receipt->client->gst_number)
                                <flux:text class="text-xs text-zinc-500 font-mono">GSTIN: {{ $receipt->client->gst_number }}</flux:text>
                            @endif
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Material Type') }}</flux:label>
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-indigo-500"></div>
                            <flux:text class="text-lg font-bold">{{ $receipt->materialType->name }}</flux:text>
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Vehicle Number') }}</flux:label>
                        <div class="flex items-center gap-2">
                            <flux:icon name="truck" class="size-4 text-zinc-400" />
                            <flux:text class="text-lg font-black uppercase tracking-tight">{{ $receipt->vehicle_number }}</flux:text>
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Royalty Number') }}</flux:label>
                        @if($receipt->royalty_number)
                            <flux:badge color="amber" class="font-mono text-sm px-3 py-1">{{ $receipt->royalty_number }}</flux:badge>
                        @else
                            <flux:text class="text-zinc-400 italic">{{ __('None') }}</flux:text>
                        @endif
                    </flux:field>

                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Dispatch Date') }}</flux:label>
                        <div class="flex items-center gap-2">
                            <flux:icon name="calendar" class="size-4 text-zinc-400" />
                            <flux:text class="font-bold">{{ $receipt->date->format('l, M d, Y') }}</flux:text>
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Dispatch Time') }}</flux:label>
                        <div class="flex items-center gap-2">
                            <flux:icon name="clock" class="size-4 text-zinc-400" />
                            <flux:text class="font-bold">{{ \Carbon\Carbon::parse($receipt->time)->format('h:i A') }}</flux:text>
                        </div>
                    </flux:field>
                </div>
            </flux:card>

            @if($receipt->remarks)
                <flux:card>
                    <div class="flex items-center gap-2 mb-4 border-b pb-2">
                        <flux:icon name="chat-bubble-left-right" class="size-5 text-zinc-400" />
                        <flux:heading size="lg">{{ __('Remarks / Notes') }}</flux:heading>
                    </div>
                    <flux:text class="italic text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $receipt->remarks }}</flux:text>
                </flux:card>
            @endif
        </div>

        {{-- Right Column: Weights & Payment --}}
        <div class="space-y-6">
            {{-- Weight Card --}}
            <flux:card class="bg-zinc-50 dark:bg-zinc-900/50 border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-2 mb-6 border-b pb-4">
                    <flux:icon name="scale" class="size-5 text-zinc-400" />
                    <flux:heading size="lg">{{ __('Weight Summary') }}</flux:heading>
                </div>

                <div class="space-y-6">
                    <div class="flex justify-between items-end border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <flux:text class="text-xs uppercase font-bold text-zinc-400">{{ __('Gross Weight') }}</flux:text>
                        <div class="text-right">
                            <span class="text-xl font-bold">{{ number_format($receipt->gross_weight, 3) }}</span>
                            <span class="text-[10px] text-zinc-400 ml-0.5 uppercase">Tons</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-end border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <flux:text class="text-xs uppercase font-bold text-zinc-400">{{ __('Tare Weight') }}</flux:text>
                        <div class="text-right">
                            <span class="text-xl font-bold">{{ number_format($receipt->tare_weight, 3) }}</span>
                            <span class="text-[10px] text-zinc-400 ml-0.5 uppercase">Tons</span>
                        </div>
                    </div>

                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                        <div class="flex justify-between items-end">
                            <flux:text class="text-xs uppercase font-black text-emerald-600 dark:text-emerald-400">{{ __('Net Weight') }}</flux:text>
                            <div class="text-right">
                                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($receipt->net_weight, 3) }}</span>
                                <span class="text-xs text-emerald-500 ml-1 uppercase">Tons</span>
                            </div>
                        </div>
                    </div>
                </div>
            </flux:card>

            {{-- Payment Card --}}
            <flux:card class="{{ $receipt->payment_type === 'cash' ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-blue-500' }}">
                <div class="flex items-center gap-2 mb-6 border-b pb-4">
                    <flux:icon name="banknotes" class="size-5 text-zinc-400" />
                    <flux:heading size="lg">{{ __('Payment Details') }}</flux:heading>
                </div>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Payment Type') }}</flux:label>
                        <div class="flex items-center gap-2">
                            @if($receipt->payment_type === 'cash')
                                <flux:badge color="emerald" icon="currency-dollar" size="sm" class="uppercase font-bold">{{ __('Cash') }}</flux:badge>
                            @elseif($receipt->payment_type === 'online')
                                <flux:badge color="blue" icon="globe-alt" size="sm" class="uppercase font-bold">{{ __('Online') }}</flux:badge>
                            @else
                                <flux:text class="text-zinc-400 italic">{{ __('Not Specified') }}</flux:text>
                            @endif
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label class="uppercase text-[10px] tracking-widest text-zinc-400 font-bold mb-1">{{ __('Amount Paid') }}</flux:label>
                        <div class="text-2xl font-black text-zinc-900 dark:text-white">
                            ₹ {{ number_format($receipt->payment_value ?: 0, 2) }}
                        </div>
                    </flux:field>
                </div>
            </flux:card>
        </div>
    </div>
</div>
