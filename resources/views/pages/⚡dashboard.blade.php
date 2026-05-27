<?php

use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Dashboard')] class extends Component {
    #[Computed]
    public function todayStats()
    {
        $todayReceipts = Receipt::whereDate('date', now())->get();

        return [
            'count' => $todayReceipts->count(),
            'weight' => $todayReceipts->sum('net_weight'),
            'clients' => Client::count(),
        ];
    }

    #[Computed]
    public function materialBreakdown()
    {
        return Receipt::whereDate('date', now())
            ->join('material_types', 'receipts.material_type_id', '=', 'material_types.id')
            ->selectRaw('material_types.name as material, sum(net_weight) as total_weight, count(*) as slip_count')
            ->groupBy('material_types.name')
            ->orderByDesc('total_weight')
            ->get();
    }

    #[Computed]
    public function recentReceipts()
    {
        return Receipt::with(['client', 'materialType'])
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function dailyTotals()
    {
        return Receipt::selectRaw('date, sum(net_weight) as total_weight, count(*) as slip_count')
            ->groupBy('date')
            ->orderByDesc('date')
            ->take(5)
            ->get();
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="font-black tracking-tight">{{ __('Operations Overview') }}</flux:heading>
            <flux:subheading class="font-medium">{{ __('Real-time tracking of quarry dispatches for') }} {{ now()->format('M d, Y') }}</flux:subheading>
        </div>
        <flux:button icon="plus" variant="primary" :href="route('receipts.create')" wire:navigate class="font-bold shadow-lg shadow-blue-500/20">
            {{ __('New Work Slip') }}
        </flux:button>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <flux:card class="relative flex flex-col gap-2 overflow-hidden border-none shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-blue-600"></div>
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600 dark:bg-blue-900/20">
                    <flux:icon name="ticket" class="size-5" />
                </div>
                <flux:text class="text-[10px] font-bold uppercase tracking-[0.1em] text-zinc-400">{{ __('Today\'s Slips') }}</flux:text>
            </div>
            <div class="mt-2 flex items-baseline gap-2 pl-2">
                <span class="text-4xl font-black tracking-tighter">{{ $this->todayStats['count'] }}</span>
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Generated') }}</span>
            </div>
        </flux:card>

        <flux:card class="relative flex flex-col gap-2 overflow-hidden border-none shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-emerald-600"></div>
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-900/20">
                    <flux:icon name="scale" class="size-5" />
                </div>
                <flux:text class="text-[10px] font-bold uppercase tracking-[0.1em] text-zinc-400">{{ __('Total Dispatched') }}</flux:text>
            </div>
            <div class="mt-2 flex items-baseline gap-2 pl-2">
                <span class="text-4xl font-black tracking-tighter text-emerald-600">{{ number_format($this->todayStats['weight'], 2) }}</span>
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Tons') }}</span>
            </div>
        </flux:card>

        <flux:card class="relative flex flex-col gap-2 overflow-hidden border-none shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-indigo-600"></div>
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600 dark:bg-indigo-900/20">
                    <flux:icon name="users" class="size-5" />
                </div>
                <flux:text class="text-[10px] font-bold uppercase tracking-[0.1em] text-zinc-400">{{ __('Total Clients') }}</flux:text>
            </div>
            <div class="mt-2 flex items-baseline gap-2 pl-2">
                <span class="text-4xl font-black tracking-tighter">{{ $this->todayStats['clients'] }}</span>
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Onboarded') }}</span>
            </div>
        </flux:card>
    </div>

    {{-- Data Section --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        {{-- Material Breakdown --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 px-1">
                <flux:icon name="chart-pie" class="size-4 text-zinc-400" />
                <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Today\'s Material Breakdown') }}</flux:heading>
            </div>
            <flux:card class="p-0 overflow-hidden border-none shadow-sm h-full">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Material') }}</flux:table.column>
                        <flux:table.column align="left" class="text-[10px] uppercase tracking-wider font-bold">{{ __('Slips') }}</flux:table.column>
                        <flux:table.column align="center" class="text-[10px] uppercase tracking-wider font-bold">{{ __('Total Weight') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->materialBreakdown as $data)
                            <flux:table.row>
                                <flux:table.cell class="font-bold text-zinc-800 dark:text-zinc-200">{{ $data->material }}</flux:table.cell>
                                <flux:table.cell align="left">
                                    <flux:badge size="sm" variant="neutral" class="font-mono">{{ $data->slip_count }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell align="center" class="font-black text-emerald-600 tabular-nums">
                                    {{ number_format($data->total_weight, 2) }} <span class="text-[9px] font-bold text-zinc-400 uppercase">T</span>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="3" align="center" class="py-12 text-zinc-400 italic font-medium">{{ __('No material dispatches today.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>

        {{-- Daily Summary --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 px-1">
                <flux:icon name="calendar-days" class="size-4 text-zinc-400" />
                <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Daily Dispatch Summary (Last 5 Days)') }}</flux:heading>
            </div>
            <flux:card class="p-0 overflow-hidden border-none shadow-sm h-full">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Date') }}</flux:table.column>
                        <flux:table.column align="center" class="text-[10px] uppercase tracking-wider font-bold">{{ __('Slips') }}</flux:table.column>
                        <flux:table.column align="center" class="text-[10px] uppercase tracking-wider font-bold">{{ __('Weight') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->dailyTotals as $data)
                            <flux:table.row>
                                <flux:table.cell class="font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}
                                </flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:badge size="sm" variant="neutral" class="font-mono">{{ $data->slip_count }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell align="center" class="font-black text-blue-600 tabular-nums">
                                    {{ number_format($data->total_weight, 2) }} <span class="text-[9px] font-bold text-zinc-400 uppercase">T</span>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="3" align="center" class="py-12 text-zinc-400 italic font-medium">{{ __('No historical data.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>

        {{-- Recent Activity --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="flex items-center gap-2 px-1">
                <flux:icon name="clock" class="size-4 text-zinc-400" />
                <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Latest Dispatches') }}</flux:heading>
            </div>
            <flux:card class="p-0 overflow-hidden border-none shadow-sm">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Client') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Vehicle') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Material') }}</flux:table.column>
                        <flux:table.column align="center" class="text-[10px] uppercase tracking-wider font-bold">{{ __('Net Weight') }}</flux:table.column>
                        <flux:table.column align="right"></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->recentReceipts as $receipt)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-bold leading-tight text-zinc-800 dark:text-zinc-200">{{ $receipt->client->name }}</span>
                                        <span class="text-[10px] text-zinc-400 font-medium tracking-tight">{{ $receipt->time }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $receipt->vehicle_number }}</flux:table.cell>
                                <flux:table.cell class="text-[11px] font-semibold text-zinc-500 uppercase tracking-tight">{{ $receipt->materialType->name }}</flux:table.cell>
                                <flux:table.cell align="center" class="font-black text-emerald-600 tabular-nums">
                                    {{ number_format($receipt->net_weight, 2) }} <span class="text-[9px] font-bold text-zinc-400 uppercase">T</span>
                                </flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:button icon="eye" size="sm" variant="ghost" :href="route('receipts.show', $receipt)" wire:navigate class="hover:bg-zinc-100" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" align="center" class="py-12 text-zinc-400 italic font-medium">{{ __('No dispatches yet.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
                @if($this->recentReceipts->count() > 0)
                    <div class="p-3 bg-zinc-50/50 dark:bg-zinc-800/20 border-t border-zinc-100 dark:border-zinc-800 text-center">
                        <flux:button variant="ghost" size="sm" :href="route('receipts.index')" wire:navigate class="font-bold text-zinc-500 hover:text-blue-600">
                            {{ __('View All Activity') }}
                        </flux:button>
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</div>
