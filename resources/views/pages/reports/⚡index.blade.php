<?php

use App\Models\Receipt;
use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

new #[Title('Dispatch Reports')] class extends Component {
    public string $date = '';
    public string $client_id = '';
    public string $month = '';

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        $this->month = now()->format('Y-m');
    }

    public function generateDaily(): void
    {
        $this->validate([
            'date' => 'required|date',
        ]);

        $url = route('reports.daily-pdf', ['date' => $this->date]);
        
        $this->js("window.open('$url', '_blank');");
    }

    public function generateMonthly(): void
    {
        $this->validate([
            'client_id' => 'nullable|exists:clients,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $url = route('reports.monthly-pdf', [
            'client_id' => $this->client_id,
            'month' => $this->month
        ]);
        
        $this->js("window.open('$url', '_blank');");
    }

    public function generateClientMaterialSummary(): void
    {
        $this->validate([
            'client_id' => 'nullable|exists:clients,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $url = route('reports.client-material-summary-pdf', [
            'client_id' => $this->client_id,
            'month' => $this->month
        ]);
        
        $this->js("window.open('$url', '_blank');");
    }

    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get();
    }
};
?>

<div class="mx-auto max-w-4xl py-6 space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex size-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
            <flux:icon name="chart-bar" class="size-6" variant="outline" />
        </div>
        <div>
            <flux:heading size="xl" class="font-bold">{{ __('Dispatch Reports') }}</flux:heading>
            <flux:subheading>{{ __('Generate daily or monthly dispatch reports') }}</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Daily Report Section --}}
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon name="calendar" class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Daily Report') }}</flux:heading>
            </div>
            
            <flux:field>
                <flux:label>{{ __('Select Date') }}</flux:label>
                <flux:input type="date" wire:model.live="date" icon="calendar" />
                <flux:error name="date" />
            </flux:field>

            {{-- Daily Stats --}}
            @php
                $dailyStats = \App\Models\Receipt::whereDate('date', $date ?: now()->toDateString())->selectRaw('count(*) as count, sum(net_weight) as weight')->first();
            @endphp
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-zinc-800 text-center">
                    <flux:text class="uppercase text-[9px] font-bold text-zinc-400 tracking-widest mb-1">{{ __('Total Slips') }}</flux:text>
                    <div class="text-xl font-black">{{ $dailyStats->count }}</div>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-zinc-800 text-center">
                    <flux:text class="uppercase text-[9px] font-bold text-zinc-400 tracking-widest mb-1">{{ __('Net Weight') }}</flux:text>
                    <div class="text-xl font-black text-emerald-600">{{ number_format($dailyStats->weight) }}<span class="text-[10px] ml-0.5">KG</span></div>
                </div>
            </div>

            <flux:button wire:click="generateDaily" variant="primary" icon="arrow-down-tray" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold">
                {{ __('Generate Daily PDF') }}
            </flux:button>
        </flux:card>

        {{-- Monthly Report Section --}}
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2 mb-2">
                <flux:icon name="calendar-days" class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Monthly Report') }}</flux:heading>
            </div>

            <livewire:autocomplete 
                wire:model.live="client_id" 
                :model="\App\Models\Client::class" 
                :placeholder="__('-- Choose Client --')" 
                :label="__('Select Client')" 
            />
            <flux:error name="client_id" />
            
            <flux:field>
                <flux:label>{{ __('Select Month') }}</flux:label>
                <flux:input type="month" wire:model.live="month" icon="calendar" />
                <flux:error name="month" />
            </flux:field>

            {{-- Monthly Stats --}}
            @php
                $monthlyStats = null;
                if ($client_id && $month) {
                    [$year, $monthNum] = explode('-', $month);
                    $monthlyStats = \App\Models\Receipt::where('client_id', $client_id)
                        ->whereYear('date', $year)
                        ->whereMonth('date', $monthNum)
                        ->selectRaw('count(*) as count, sum(net_weight) as weight')
                        ->first();
                }
            @endphp
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-zinc-800 text-center">
                    <flux:text class="uppercase text-[9px] font-bold text-zinc-400 tracking-widest mb-1">{{ __('Month Slips') }}</flux:text>
                    <div class="text-xl font-black">{{ $monthlyStats ? $monthlyStats->count : 0 }}</div>
                </div>
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-zinc-800 text-center">
                    <flux:text class="uppercase text-[9px] font-bold text-zinc-400 tracking-widest mb-1">{{ __('Month Weight') }}</flux:text>
                    <div class="text-xl font-black text-indigo-600">{{ $monthlyStats ? number_format($monthlyStats->weight) : '0' }}<span class="text-[10px] ml-0.5">KG</span></div>
                </div>
            </div>

            <div class="space-y-3">
                <flux:button wire:click="generateMonthly" variant="primary" icon="arrow-down-tray" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold">
                    {{ __('Generate Monthly PDF') }}
                </flux:button>

                <flux:button wire:click="generateClientMaterialSummary" variant="primary" icon="document-chart-bar" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold">
                    {{ __('Generate Material Summary PDF') }}
                </flux:button>
            </div>
        </flux:card>
    </div>
</div>
