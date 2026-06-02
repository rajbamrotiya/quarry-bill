<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Dispatch (Sell)')" class="grid bg-blue-50/50 dark:bg-blue-900/20 p-3 rounded-xl border border-blue-100/50 dark:border-blue-800/30">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('clients.index')" :current="request()->routeIs('clients.*')" wire:navigate>
                        {{ __('Clients') }}
                    </flux:sidebar.item>
                    <div class="my-1">
                        <flux:button variant="primary" icon="plus-circle" :href="route('receipts.create')" wire:navigate class="w-full justify-start font-bold shadow-sm mb-1 bg-blue-600 hover:bg-blue-700 text-white">
                            {{ __('New Dispatch Receipt') }}
                        </flux:button>
                    </div>
                    <flux:sidebar.item icon="ticket" :href="route('receipts.index')" :current="request()->routeIs('receipts.index', 'receipts.show', 'receipts.edit')" wire:navigate>
                        {{ __('Dispatch Receipts') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-bar" :href="route('reports.index')" :current="request()->routeIs('reports.*')" wire:navigate>
                        {{ __('Dispatch Reports') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Procurement (Buy)')" class="grid bg-emerald-50/50 dark:bg-emerald-900/20 p-3 rounded-xl border border-emerald-100/50 dark:border-emerald-800/30 mt-4">
                    <flux:sidebar.item icon="shopping-cart" :href="route('buy-dashboard')" :current="request()->routeIs('buy-dashboard')" wire:navigate>
                        {{ __('Buy Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" :href="route('suppliers.index')" :current="request()->routeIs('suppliers.*')" wire:navigate>
                        {{ __('Suppliers') }}
                    </flux:sidebar.item>
                    <div class="my-1">
                        <flux:button variant="primary" icon="plus-circle" :href="route('buy-receipts.create')" wire:navigate class="w-full justify-start font-bold shadow-sm mb-1 bg-emerald-600 hover:bg-emerald-700 border-emerald-600 text-white">
                            {{ __('New Buy Receipt') }}
                        </flux:button>
                    </div>
                    <flux:sidebar.item icon="shopping-bag" :href="route('buy-receipts.index')" :current="request()->routeIs('buy-receipts.index', 'buy-receipts.show', 'buy-receipts.edit')" wire:navigate>
                        {{ __('Buy Receipts') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-pie" :href="route('buy-reports.index')" :current="request()->routeIs('buy-reports.*')" wire:navigate>
                        {{ __('Buy Reports') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Master Data')" class="grid bg-amber-50/50 dark:bg-amber-900/20 p-3 rounded-xl border border-amber-100/50 dark:border-amber-800/30 mt-4">
                    <flux:sidebar.item icon="tag" :href="route('material-types.index')" :current="request()->routeIs('material-types.*')" wire:navigate>
                        {{ __('Material Types') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
