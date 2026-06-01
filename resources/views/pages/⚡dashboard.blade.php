<?php

use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Dashboard')] class extends Component {
    #[Computed]
    public function stats()
    {
        $today = Receipt::whereDate('date', now())->selectRaw('count(*) as count, sum(net_weight) as weight')->first();
        $thisWeek = Receipt::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->selectRaw('count(*) as count, sum(net_weight) as weight')->first();
        $thisMonth = Receipt::whereMonth('date', now()->month)->whereYear('date', now()->year)->selectRaw('count(*) as count, sum(net_weight) as weight')->first();

        return [
            'today' => [
                'count' => $today->count ?? 0,
                'weight' => $today->weight ?? 0,
            ],
            'week' => [
                'count' => $thisWeek->count ?? 0,
                'weight' => $thisWeek->weight ?? 0,
            ],
            'month' => [
                'count' => $thisMonth->count ?? 0,
                'weight' => $thisMonth->weight ?? 0,
            ],
            'total_clients' => Client::count(),
            'total_materials' => MaterialType::count(),
        ];
    }

    #[Computed]
    public function materialBreakdown()
    {
        return Receipt::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
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
            ->take(6)
            ->get();
    }

    #[Computed]
    public function dailyTotals()
    {
        return Receipt::selectRaw('date, sum(net_weight) as total_weight, count(*) as slip_count')
            ->groupBy('date')
            ->orderByDesc('date')
            ->take(7)
            ->get();
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="flex size-14 items-center justify-center rounded-2xl bg-zinc-900 text-white shadow-xl">
                <flux:icon name="chart-pie" class="size-7" variant="outline" />
            </div>
            <div>
                <flux:heading size="xl" class="font-black tracking-tight leading-none">{{ __('Dispatch Analytics') }}</flux:heading>
                <flux:subheading class="font-medium mt-1">{{ __('Operational overview for') }} {{ now()->format('F Y') }}</flux:subheading>
            </div>
        </div>
        <div class="flex gap-3">
            <flux:button icon="plus" variant="primary" :href="route('receipts.create')" wire:navigate class="font-bold shadow-lg shadow-blue-500/20 px-6 py-2.5 rounded-xl">
                {{ __('New Work Slip') }}
            </flux:button>
        </div>
    </div>

    {{-- Performance Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
        {{-- Today's Weight --}}
        <flux:card class="relative p-6 overflow-hidden border-none shadow-sm bg-blue-600 text-white group">
            <div class="absolute -right-6 -top-6 size-24 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center gap-3 opacity-90">
                <flux:icon name="scale" class="size-5" />
                <flux:text class="text-[10px] font-bold uppercase tracking-[0.15em] text-blue-100">{{ __('Today\'s Dispatch') }}</flux:text>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-4xl font-black tracking-tighter leading-none">{{ number_format($this->stats['today']['weight'], 2) }}</span>
                <span class="text-xs font-bold text-blue-100 uppercase tracking-widest">{{ __('KG') }}</span>
            </div>
            <div class="mt-2 text-[10px] font-semibold text-blue-100/70 uppercase tracking-wider">
                {{ $this->stats['today']['count'] }} {{ __('Total Slips Today') }}
            </div>
        </flux:card>

        {{-- Weekly Weight --}}
        <flux:card class="relative p-6 overflow-hidden border-none shadow-sm bg-indigo-600 text-white group">
            <div class="absolute -right-6 -top-6 size-24 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center gap-3 opacity-90">
                <flux:icon name="calendar" class="size-5" />
                <flux:text class="text-[10px] font-bold uppercase tracking-[0.15em] text-indigo-100">{{ __('Weekly Performance') }}</flux:text>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-4xl font-black tracking-tighter leading-none">{{ number_format($this->stats['week']['weight'], 2) }}</span>
                <span class="text-xs font-bold text-indigo-100 uppercase tracking-widest">{{ __('KG') }}</span>
            </div>
            <div class="mt-2 text-[10px] font-semibold text-indigo-100/70 uppercase tracking-wider">
                {{ $this->stats['week']['count'] }} {{ __('Slips this week') }}
            </div>
        </flux:card>

        {{-- Monthly Weight --}}
        <flux:card class="relative p-6 overflow-hidden border-none shadow-sm bg-zinc-900 text-white group">
            <div class="absolute -right-6 -top-6 size-24 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-center gap-3 opacity-90">
                <flux:icon name="chart-bar" class="size-5" />
                <flux:text class="text-[10px] font-bold uppercase tracking-[0.15em] text-zinc-400">{{ __('Monthly Volume') }}</flux:text>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-4xl font-black tracking-tighter leading-none">{{ number_format($this->stats['month']['weight'], 2) }}</span>
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('KG') }}</span>
            </div>
            <div class="mt-2 text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">
                {{ $this->stats['month']['count'] }} {{ __('Total dispatches') }}
            </div>
        </flux:card>

        {{-- Entity Count --}}
        <div class="grid grid-rows-2 gap-4">
            <flux:card class="flex items-center gap-4 p-4 overflow-hidden border-none shadow-sm bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800">
                <div class="rounded-xl bg-white dark:bg-zinc-800 p-2 text-zinc-900 dark:text-white shadow-sm">
                    <flux:icon name="users" class="size-5" />
                </div>
                <div>
                    <flux:text class="text-[9px] font-bold uppercase tracking-[0.15em] text-zinc-400 leading-none">{{ __('Clients') }}</flux:text>
                    <div class="text-xl font-black mt-1 leading-none">{{ $this->stats['total_clients'] }}</div>
                </div>
            </flux:card>
            <flux:card class="flex items-center gap-4 p-4 overflow-hidden border-none shadow-sm bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800">
                <div class="rounded-xl bg-white dark:bg-zinc-800 p-2 text-zinc-900 dark:text-white shadow-sm">
                    <flux:icon name="cube" class="size-5" />
                </div>
                <div>
                    <flux:text class="text-[9px] font-bold uppercase tracking-[0.15em] text-zinc-400 leading-none">{{ __('Materials') }}</flux:text>
                    <div class="text-xl font-black mt-1 leading-none">{{ $this->stats['total_materials'] }}</div>
                </div>
            </flux:card>
        </div>
    </div>

    {{-- Main Analytics Section --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        {{-- Historical Performance --}}
        <div class="space-y-4 lg:col-span-8">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center gap-2">
                    <flux:icon name="presentation-chart-line" class="size-4 text-zinc-400" />
                    <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Dispatch Trends (Last 7 Days)') }}</flux:heading>
                </div>
                <flux:badge size="sm" variant="neutral" class="text-[10px] font-bold uppercase tracking-tighter">{{ __('Metric: KG') }}</flux:badge>
            </div>
            
            <flux:card class="p-0 overflow-hidden border-none shadow-sm bg-white dark:bg-zinc-900">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold pl-6">{{ __('Date') }}</flux:table.column>
                        <flux:table.column align="center" class="text-[10px] uppercase tracking-wider font-bold">{{ __('Slips') }}</flux:table.column>
                        <flux:table.column align="right" class="text-[10px] uppercase tracking-wider font-bold pr-6">{{ __('Weight') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->dailyTotals as $data)
                            <flux:table.row class="group">
                                <flux:table.cell class="pl-6">
                                    <div class="flex items-center gap-3">
                                        <div class="size-2 rounded-full @if(\Carbon\Carbon::parse($data->date)->isToday()) bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)] @else bg-zinc-200 dark:bg-zinc-700 @endif"></div>
                                        <span class="font-bold text-zinc-800 dark:text-zinc-200 @if(\Carbon\Carbon::parse($data->date)->isToday()) text-blue-600 @endif">
                                            {{ \Carbon\Carbon::parse($data->date)->format('D, M d') }}
                                        </span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell align="center">
                                    <flux:badge size="sm" variant="neutral" class="font-mono px-2 py-0.5">{{ $data->slip_count }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell align="right" class="pr-6 font-black tabular-nums">
                                    <span class="text-zinc-900 dark:text-white">{{ number_format($data->total_weight, 2) }}</span>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase ml-1 tracking-tighter">{{ __('KG') }}</span>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="3" align="center" class="py-12 text-zinc-400 italic font-medium">{{ __('No historical data found.') }}</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>

        {{-- Material Breakdown Sidebar --}}
        <div class="space-y-4 lg:col-span-4">
            <div class="flex items-center gap-2 px-1">
                <flux:icon name="adjustments-horizontal" class="size-4 text-zinc-400" />
                <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Monthly Product Mix') }}</flux:heading>
            </div>
            
            <flux:card class="p-6 border-none shadow-sm space-y-6 bg-white dark:bg-zinc-900">
                <div class="space-y-5">
                    @forelse ($this->materialBreakdown as $data)
                        <div class="space-y-2">
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">{{ $data->material }}</span>
                                <span class="text-[11px] font-black text-zinc-900 dark:text-white">
                                    {{ number_format($data->total_weight, 1) }} <span class="text-zinc-400 font-bold uppercase">{{ __('T') }}</span>
                                </span>
                            </div>
                            <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                @php
                                    $totalMonthWeight = $this->stats['month']['weight'];
                                    $percentage = $totalMonthWeight > 0 ? ($data->total_weight / $totalMonthWeight) * 100 : 0;
                                @endphp
                                <div class="h-full bg-zinc-900 dark:bg-zinc-700 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between text-[9px] font-bold uppercase tracking-widest text-zinc-400">
                                <span>{{ $data->slip_count }} {{ __('Dispatches') }}</span>
                                <span>{{ round($percentage) }}%</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <flux:icon name="cube-transparent" class="size-8 text-zinc-200 mx-auto mb-3" />
                            <flux:text class="italic text-zinc-400">{{ __('No products dispatched this month.') }}</flux:text>
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </div>

        {{-- Latest Activity - Full Width --}}
        <div class="space-y-4 lg:col-span-12">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="size-4 text-zinc-400" />
                    <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Live Dispatch Feed') }}</flux:heading>
                </div>
                <flux:button variant="ghost" size="xs" :href="route('receipts.index')" wire:navigate class="font-bold text-zinc-400 hover:text-zinc-900 uppercase tracking-widest text-[9px]">
                    {{ __('View History') }}
                </flux:button>
            </div>

            <flux:card class="p-0 overflow-hidden border-none shadow-sm bg-white dark:bg-zinc-900">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold pl-6">{{ __('Pass #') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Client & Destination') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Vehicle') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Material') }}</flux:table.column>
                        <flux:table.column align="right" class="text-[10px] uppercase tracking-wider font-bold pr-6">{{ __('Net Payload') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->recentReceipts as $receipt)
                            <flux:table.row class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <flux:table.cell class="pl-6 font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-tighter">
                                    #{{ $receipt->pass_number ?: str_pad($receipt->id, 5, '0', STR_PAD_LEFT) }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-black leading-tight text-zinc-900 dark:text-white uppercase text-xs">{{ $receipt->client->name }}</span>
                                        <span class="text-[10px] text-zinc-400 font-bold tracking-tight mt-0.5 uppercase">{{ $receipt->time }} • {{ $receipt->date->format('M d') }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" variant="neutral" class="font-mono text-[10px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 border-none">{{ $receipt->vehicle_number }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $receipt->materialType->name }}</span>
                                </flux:table.cell>
                                <flux:table.cell align="right" class="pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-black text-emerald-600 tabular-nums text-sm">
                                            {{ number_format($receipt->net_weight, 2) }}
                                        </span>
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-tighter">{{ __('KG') }}</span>
                                        <flux:button icon="eye" size="sm" variant="ghost" :href="route('receipts.show', $receipt)" wire:navigate class="ml-2" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" align="center" class="py-16">
                                    <div class="flex flex-col items-center gap-2">
                                        <flux:icon name="document-magnifying-glass" class="size-8 text-zinc-100" />
                                        <flux:text class="italic text-zinc-400 font-medium">{{ __('Waiting for today\'s first dispatch...') }}</flux:text>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>
</div>
