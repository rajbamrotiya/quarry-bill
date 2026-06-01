<?php

use App\Models\Supplier;
use App\Models\MaterialType;
use App\Models\BuyReceipt;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Buy Dashboard')] class extends Component {
    #[Computed]
    public function stats()
    {
        $today = BuyReceipt::whereDate('date', now())->selectRaw('count(*) as count, sum(net_weight) as weight')->first();
        $thisWeek = BuyReceipt::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->selectRaw('count(*) as count, sum(net_weight) as weight')->first();
        $thisMonth = BuyReceipt::whereMonth('date', now()->month)->whereYear('date', now()->year)->selectRaw('count(*) as count, sum(net_weight) as weight')->first();

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
            'total_suppliers' => Supplier::count(),
            'total_materials' => MaterialType::count(),
        ];
    }

    #[Computed]
    public function materialBreakdown()
    {
        return BuyReceipt::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->join('material_types', 'buy_receipts.material_type_id', '=', 'material_types.id')
            ->selectRaw('material_types.name as material, sum(net_weight) as total_weight, count(*) as slip_count')
            ->groupBy('material_types.name')
            ->orderByDesc('total_weight')
            ->get();
    }

    #[Computed]
    public function recentBuyReceipts()
    {
        return BuyReceipt::with(['supplier', 'materialType'])
            ->latest()
            ->take(6)
            ->get();
    }

    #[Computed]
    public function dailyTotals()
    {
        return BuyReceipt::selectRaw('date, sum(net_weight) as total_weight, count(*) as slip_count')
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
                <flux:heading size="xl" class="font-black tracking-tight leading-none">{{ __('Buy Analytics') }}</flux:heading>
                <flux:subheading class="font-medium mt-1">{{ __('Operational overview for') }} {{ now()->format('F Y') }}</flux:subheading>
            </div>
        </div>
        <div class="flex gap-3">
            <flux:button icon="plus" variant="primary" :href="route('buy-receipts.create')" wire:navigate class="font-bold shadow-lg shadow-blue-500/20 px-6 py-2.5 rounded-xl">
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
                {{ $this->stats['month']['count'] }} {{ __('Total buyes') }}
            </div>
        </flux:card>

        {{-- Entity Count --}}
        <div class="grid grid-rows-2 gap-4">
            <flux:card class="flex items-center gap-4 p-4 overflow-hidden border-none shadow-sm bg-zinc-50/50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800">
                <div class="rounded-xl bg-white dark:bg-zinc-800 p-2 text-zinc-900 dark:text-white shadow-sm">
                    <flux:icon name="users" class="size-5" />
                </div>
                <div>
                    <flux:text class="text-[9px] font-bold uppercase tracking-[0.15em] text-zinc-400 leading-none">{{ __('Suppliers') }}</flux:text>
                    <div class="text-xl font-black mt-1 leading-none">{{ $this->stats['total_suppliers'] }}</div>
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
                    <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Buy Trends (Last 7 Days)') }}</flux:heading>
                </div>
                <flux:badge size="sm" variant="neutral" class="text-[10px] font-bold uppercase tracking-tighter">{{ __('Metric: KG') }}</flux:badge>
            </div>
            
            <flux:card class="p-4 border-none shadow-sm bg-white dark:bg-zinc-900">
                <div class="h-64 w-full relative" x-data="{
                    init() {
                        const labels = @js($this->dailyTotals->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray());
                        const data = @js($this->dailyTotals->pluck('weight')->toArray());
                        
                        new window.Chart(this.$refs.canvas, {
                            type: 'line',
                            data: {
                                labels: labels.reverse(),
                                datasets: [{
                                    label: 'Weight (KG)',
                                    data: data.reverse(),
                                    borderColor: '#4f46e5',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#fff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e5e7eb' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                }">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </flux:card>
        </div>

        {{-- Material Breakdown Sidebar --}}
        <div class="space-y-4 lg:col-span-4">
            <div class="flex items-center gap-2 px-1">
                <flux:icon name="adjustments-horizontal" class="size-4 text-zinc-400" />
                <flux:heading class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Monthly Product Mix') }}</flux:heading>
            </div>
            
            <flux:card class="p-6 border-none shadow-sm bg-white dark:bg-zinc-900">
                <div class="h-64 relative flex justify-center items-center" x-data="{
                    init() {
                        const labels = @js($this->materialBreakdown->pluck('material')->toArray());
                        const data = @js($this->materialBreakdown->pluck('total_weight')->toArray());
                        const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#3b82f6'];
                        
                        if(data.length === 0) return;

                        new window.Chart(this.$refs.canvas, {
                            type: 'doughnut',
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: data,
                                    backgroundColor: colors.slice(0, data.length),
                                    borderWidth: 0,
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } }
                                }
                            }
                        });
                    }
                }">
                    @if(count($this->materialBreakdown) > 0)
                        <canvas x-ref="canvas"></canvas>
                    @else
                        <div class="text-zinc-400 text-sm font-bold uppercase w-full text-center">No Data for this month</div>
                    @endif
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
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Supplier & Destination') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Vehicle') }}</flux:table.column>
                        <flux:table.column class="text-[10px] uppercase tracking-wider font-bold">{{ __('Material') }}</flux:table.column>
                        <flux:table.column align="right" class="text-[10px] uppercase tracking-wider font-bold pr-6">{{ __('Net Payload') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->recentBuyReceipts as $buy_receipt)
                            <flux:table.row class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <flux:table.cell class="pl-6 font-mono text-[11px] font-bold text-zinc-400 uppercase tracking-tighter">
                                    #{{ $buy_receipt->pass_number ?: str_pad($buy_receipt->id, 5, '0', STR_PAD_LEFT) }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex flex-col">
                                        <span class="font-black leading-tight text-zinc-900 dark:text-white uppercase text-xs">{{ $buy_receipt->supplier->name }}</span>
                                        <span class="text-[10px] text-zinc-400 font-bold tracking-tight mt-0.5 uppercase">{{ $buy_receipt->time }} • {{ $buy_receipt->date->format('M d') }}</span>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" variant="neutral" class="font-mono text-[10px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 border-none">{{ $buy_receipt->vehicle_number }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $buy_receipt->materialType->name }}</span>
                                </flux:table.cell>
                                <flux:table.cell align="right" class="pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-black text-emerald-600 tabular-nums text-sm">
                                            {{ number_format($buy_receipt->net_weight, 2) }}
                                        </span>
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-tighter">{{ __('KG') }}</span>
                                        <flux:button icon="eye" size="sm" variant="ghost" :href="route('buy-receipts.show', $buy_receipt)" wire:navigate class="ml-2" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" align="center" class="py-16">
                                    <div class="flex flex-col items-center gap-2">
                                        <flux:icon name="document-magnifying-glass" class="size-8 text-zinc-100" />
                                        <flux:text class="italic text-zinc-400 font-medium">{{ __('Waiting for today\'s first buy...') }}</flux:text>
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
