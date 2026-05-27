<?php

use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Daily Reports')] class extends Component {
    public string $date = '';

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function generate(): void
    {
        $this->validate([
            'date' => 'required|date',
        ]);

        $url = route('reports.daily-pdf', ['date' => $this->date]);
        
        $this->js("window.open('$url', '_blank');");
    }
};
?>

<div class="mx-auto max-w-2xl py-6">
    <div class="mb-8 flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
            <flux:icon name="chart-bar" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Daily Dispatch Reports') }}</flux:heading>
            <flux:subheading>{{ __('Generate a comprehensive horizontal dispatch report by date') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Select Date for Report') }}</flux:label>
            <flux:input type="date" wire:model="date" icon="calendar" />
            <flux:error name="date" />
        </flux:field>

        <div class="flex justify-end">
            <flux:button wire:click="generate" variant="primary" class="bg-zinc-900 px-8 py-6 rounded-2xl font-bold gap-2">
                <flux:icon name="arrow-down-tray" class="size-5" />
                {{ __('Preview & Download Report') }}
            </flux:button>
        </div>
    </flux:card>

    {{-- Stats Summary for the selected date --}}
    @php
        $stats = \App\Models\Receipt::whereDate('date', $date)->selectRaw('count(*) as count, sum(net_weight) as weight')->first();
    @endphp
    
    <div class="mt-8 grid grid-cols-2 gap-6">
        <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
            <flux:text class="uppercase text-[10px] font-bold text-zinc-400 tracking-widest mb-1">{{ __('Total Dispatches') }}</flux:text>
            <div class="text-3xl font-black">{{ $stats->count }}</div>
        </div>
        <div class="p-6 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
            <flux:text class="uppercase text-[10px] font-bold text-zinc-400 tracking-widest mb-1">{{ __('Total Net Weight') }}</flux:text>
            <div class="text-3xl font-black text-emerald-600">{{ number_format($stats->weight, 3) }} <span class="text-xs text-zinc-400">T</span></div>
        </div>
    </div>
</div>
