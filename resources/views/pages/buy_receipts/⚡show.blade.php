<?php

use App\Models\BuyReceipt;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('BuyReceipt Details')] class extends Component {
    public BuyReceipt $buy_receipt;

    public string $tab = 'details';

    public function mount(BuyReceipt $buy_receipt): void
    {
        $this->buy_receipt = $buy_receipt->load(['supplier', 'materialType', 'histories.user']);
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-5xl mx-auto py-6">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b pb-6">
        <div class="flex items-center gap-4">
            <flux:button icon="arrow-left" variant="ghost" :href="route('buy-receipts.index')" wire:navigate />
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-black">{{ __('BuyReceipt Details') }}</flux:heading>
                    <flux:badge color="indigo" size="sm" inset="top" class="font-mono">#{{ $buy_receipt->pass_number ?: str_pad($buy_receipt->id, 10, '0', STR_PAD_LEFT) }}</flux:badge>
                </div>
                <flux:subheading>{{ __('Detailed information and modification logs') }}</flux:subheading>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <flux:button icon="arrow-down-tray" variant="ghost" :href="route('buy-receipts.pdf', $buy_receipt)" target="_blank">
                {{ __('Download PDF') }}
            </flux:button>
            <flux:button icon="pencil-square" variant="primary" :href="route('buy-receipts.edit', $buy_receipt)" wire:navigate class="bg-indigo-600 border-indigo-600">
                {{ __('Edit BuyReceipt') }}
            </flux:button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-zinc-100 dark:bg-zinc-800/50 p-1.5 rounded-xl flex items-center gap-1 w-full max-w-md shadow-inner">
        <button 
            wire:click="selectTab('details')" 
            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold rounded-lg transition-all {{ $tab === 'details' ? 'bg-white dark:bg-zinc-900 text-indigo-600 shadow-sm ring-1 ring-zinc-200 dark:ring-zinc-700' : 'text-zinc-500 hover:text-zinc-700 hover:bg-zinc-200/50 dark:hover:bg-zinc-800' }}"
        >
            <flux:icon name="identification" class="size-4" />
            {{ __('Details') }}
        </button>
        <button 
            wire:click="selectTab('history')" 
            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold rounded-lg transition-all {{ $tab === 'history' ? 'bg-white dark:bg-zinc-900 text-indigo-600 shadow-sm ring-1 ring-zinc-200 dark:ring-zinc-700' : 'text-zinc-500 hover:text-zinc-700 hover:bg-zinc-200/50 dark:hover:bg-zinc-800' }}"
        >
            <flux:icon name="clock" class="size-4" />
            {{ __('Update History') }}
            @if($buy_receipt->histories->count() > 1)
                <flux:badge size="sm" color="indigo" class="px-1.5 py-0">{{ $buy_receipt->histories->count() - 1 }}</flux:badge>
            @endif
        </button>
    </div>

    @if($tab === 'details')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-in fade-in duration-300">
            {{-- Left Column: Core Info --}}
            <div class="lg:col-span-2 space-y-6">
                <flux:card>
                    <div class="flex items-center gap-2 mb-6 border-b pb-4">
                        <flux:icon name="identification" class="size-5 text-zinc-400" />
                        <flux:heading size="lg">{{ __('Dispatch Information') }}</flux:heading>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                        <flux:field>
                            <flux:label class="uppercase text-sm tracking-widest text-teal-500 font-bold mb-1">{{ __('Consignee / Supplier') }}</flux:label>
                            <div class="flex flex-col">
                                <flux:text class="text-lg font-bold text-zinc-900 dark:text-white">{{ $buy_receipt->supplier->name }}</flux:text>
                                @if($buy_receipt->supplier->gst_number)
                                    <flux:text class="text-xs text-zinc-500 font-mono">GSTIN: {{ $buy_receipt->supplier->gst_number }}</flux:text>
                                @endif
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label class="uppercase text-sm tracking-widest text-rose-500 font-bold mb-1">{{ __('Material Type') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <div class="size-2 rounded-full bg-indigo-500"></div>
                                <flux:text class="text-lg font-bold">{{ $buy_receipt->materialType->name }}</flux:text>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label class="uppercase text-sm tracking-widest text-amber-500 font-bold mb-1">{{ __('Vehicle Number') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:icon name="truck" class="size-4 text-zinc-400" />
                                <flux:text class="text-lg font-black uppercase tracking-tight">{{ $buy_receipt->vehicle_number }}</flux:text>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label class="uppercase text-sm tracking-widest text-pink-500 font-bold mb-1">{{ __('Royalty Number') }}</flux:label>
                            @if($buy_receipt->royalty_number)
                                <flux:badge color="amber" class="font-mono text-sm px-3 py-1">{{ $buy_receipt->royalty_number }}</flux:badge>
                            @else
                                <flux:text class="text-zinc-400 italic">{{ __('None') }}</flux:text>
                            @endif
                        </flux:field>

                        <flux:field>
                            <flux:label class="uppercase text-sm tracking-widest text-sky-500 font-bold mb-1">{{ __('Dispatch Date') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:icon name="calendar" class="size-4 text-zinc-400" />
                                <flux:text class="font-bold">{{ $buy_receipt->date->format('l, M d, Y') }}</flux:text>
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label class="uppercase text-sm tracking-widest text-violet-500 font-bold mb-1">{{ __('Dispatch Time') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:icon name="clock" class="size-4 text-zinc-400" />
                                <flux:text class="font-bold">{{ \Carbon\Carbon::parse($buy_receipt->time)->format('h:i A') }}</flux:text>
                            </div>
                        </flux:field>
                    </div>
                </flux:card>

                @if($buy_receipt->remarks)
                    <flux:card>
                        <div class="flex items-center gap-2 mb-4 border-b pb-2">
                            <flux:icon name="chat-bubble-left-right" class="size-5 text-zinc-400" />
                            <flux:heading size="lg">{{ __('Remarks / Notes') }}</flux:heading>
                        </div>
                        <flux:text class="italic text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $buy_receipt->remarks }}</flux:text>
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
                                <span class="text-xl font-bold">{{ number_format($buy_receipt->gross_weight) }}</span>
                                <span class="text-[10px] text-zinc-400 ml-0.5 uppercase">KG</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-end border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            <flux:text class="text-xs uppercase font-bold text-zinc-400">{{ __('Tare Weight') }}</flux:text>
                            <div class="text-right">
                                <span class="text-xl font-bold">{{ number_format($buy_receipt->tare_weight) }}</span>
                                <span class="text-[10px] text-zinc-400 ml-0.5 uppercase">KG</span>
                            </div>
                        </div>

                        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                            <div class="flex justify-between items-end">
                                <flux:text class="text-xs uppercase font-black text-emerald-600 dark:text-emerald-400">{{ __('Net Weight') }}</flux:text>
                                <div class="text-right">
                                    <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($buy_receipt->net_weight) }}</span>
                                    <span class="text-xs text-emerald-500 ml-1 uppercase">KG</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </flux:card>

                
            </div>
        </div>
    @else
        <div class="animate-in fade-in slide-in-from-bottom-4 duration-300">
            <flux:card class="p-0 overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:heading size="lg">{{ __('Modification Log') }}</flux:heading>
                    <flux:subheading>{{ __('Timeline of all changes made to this buy_receipt') }}</flux:subheading>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($buy_receipt->histories as $history)
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="mt-1">
                                    @if($history->event === 'created')
                                        <div class="size-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center">
                                            <flux:icon name="plus-circle" class="size-5" />
                                        </div>
                                    @else
                                        <div class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center">
                                            <flux:icon name="pencil-square" class="size-5" />
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <flux:text class="font-bold text-zinc-900 dark:text-white">
                                                {{ $history->event === 'created' ? __('BuyReceipt Created') : __('BuyReceipt Updated') }}
                                            </flux:text>
                                            <flux:text class="text-xs text-zinc-400">
                                                {{ __('by') }} {{ $history->user?->name ?: __('System') }} • {{ $history->created_at->format('M d, Y h:i A') }}
                                            </flux:text>
                                        </div>
                                    </div>

                                    @if($history->event === 'updated')
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($history->changes as $field => $values)
                                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border border-zinc-100 dark:border-zinc-800/50">
                                                    <div class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">{{ str_replace('_', ' ', $field) }}</div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex flex-col flex-1">
                                                            <span class="text-[10px] text-zinc-400 font-bold uppercase">{{ __('Old') }}</span>
                                                            <span class="text-xs font-medium text-zinc-500 line-through truncate">{{ is_array($values['old']) ? json_encode($values['old']) : $values['old'] }}</span>
                                                        </div>
                                                        <flux:icon name="arrow-right" class="size-3 text-zinc-300" />
                                                        <div class="flex flex-col flex-1 text-right">
                                                            <span class="text-[10px] text-blue-400 font-bold uppercase">{{ __('New') }}</span>
                                                            <span class="text-xs font-black text-zinc-900 dark:text-white truncate">{{ is_array($values['new']) ? json_encode($values['new']) : $values['new'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-3 rounded-lg border border-emerald-100 dark:border-emerald-900/30">
                                            <flux:text class="text-xs text-emerald-700 dark:text-emerald-400 font-medium italic">
                                                {{ __('Initial record created with all baseline weights and details.') }}
                                            </flux:text>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <flux:icon name="clock" class="size-12 text-zinc-100 mx-auto mb-4" />
                            <flux:text class="italic text-zinc-400">{{ __('No history available for this buy_receipt.') }}</flux:text>
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </div>
    @endif
</div>
</div>
