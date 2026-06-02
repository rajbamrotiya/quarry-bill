<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Quarry Bill') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/js/app.js', 'resources/css/app.css'])
    </head>
    <body class="h-full bg-white font-sans text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100">
        <div class="flex min-h-full flex-col">
            <header class="border-b border-zinc-200 bg-white/80 backdrop-blur-md sticky top-0 z-50 dark:border-zinc-800 dark:bg-zinc-900/80">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-900 dark:bg-white">
                            <x-app-logo-icon class="size-6 text-white dark:text-zinc-900" />
                        </div>
                        <span class="text-xl font-bold tracking-tight">{{ config('app.name', 'Quarry Bill') }}</span>
                    </div>

                    <nav class="flex items-center gap-4">
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary">Dashboard</flux:button>
                        @else
                            <flux:button href="{{ route('login') }}" variant="ghost">Log in</flux:button>
                            @if (Route::has('register'))
                                <flux:button href="{{ route('register') }}" variant="primary">Register</flux:button>
                            @endif
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="flex-1">
                {{-- Hero Section --}}
                <div class="relative isolate overflow-hidden bg-white dark:bg-zinc-900" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                    <!-- Background Gradients -->
                    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-accent to-blue-500 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
                    </div>
                    
                    <div class="mx-auto max-w-7xl px-6 pt-10 pb-24 sm:pb-32 lg:flex lg:px-8 lg:py-40">
                        <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl lg:flex-shrink-0 lg:pt-8" 
                             x-show="show" x-transition:enter="transition ease-out duration-1000 transform" 
                             x-transition:enter-start="opacity-0 translate-y-12" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                            <flux:badge size="md" variant="pill" class="mb-6 bg-accent/10 text-accent font-semibold border-none">🚀 The Complete Quarry Management System</flux:badge>
                            <flux:heading size="xl" level="1" class="text-5xl font-black tracking-tight sm:text-7xl">
                                Intelligent Operations for <span class="text-accent">Modern Quarries</span>
                            </flux:heading>
                            <flux:text class="mt-6 text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                                Unify your Dispatch (Sales) and Procurement (Purchases) in one powerful platform. Generate PDF receipts instantly, track material mix, and drive decisions with real-time analytics.
                            </flux:text>
                            <div class="mt-10 flex items-center gap-x-6">
                                @auth
                                    <flux:button href="{{ route('dashboard') }}" variant="primary" class="rounded-full px-8 py-3 font-bold transition-transform hover:scale-105">Go to Dashboard</flux:button>
                                @else
                                    <flux:button href="{{ route('login') }}" variant="primary" class="rounded-full px-8 py-3 font-bold transition-transform hover:scale-105">Log in to Access</flux:button>
                                @endauth
                                <flux:button href="#features" variant="ghost" icon-trailing="arrow-down" class="rounded-full">Explore Features</flux:button>
                            </div>
                        </div>
                        <div class="mx-auto mt-16 flex max-w-2xl sm:mt-24 lg:ml-10 lg:mr-0 lg:mt-0 lg:max-w-none lg:flex-none xl:ml-32"
                             x-show="show" x-transition:enter="transition ease-out duration-1000 delay-300 transform" 
                             x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                            <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none">
                                <div class="relative rounded-2xl bg-zinc-900/5 p-2 ring-1 ring-inset ring-zinc-900/10 dark:bg-white/5 dark:ring-white/10 lg:-m-4 lg:rounded-2xl lg:p-4 hover:shadow-2xl hover:shadow-accent/20 transition-all duration-700 group">
                                    <div class="w-[30rem] lg:w-[40rem] aspect-[16/10] bg-white dark:bg-zinc-950 rounded-lg shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 p-8 flex flex-col gap-6 relative">
                                        <!-- Decorative floating elements -->
                                        <div class="absolute -top-10 -right-10 size-40 bg-accent/20 blur-3xl rounded-full group-hover:bg-accent/40 transition-all duration-700"></div>
                                        <div class="absolute -bottom-10 -left-10 size-40 bg-blue-500/20 blur-3xl rounded-full group-hover:bg-blue-500/40 transition-all duration-700"></div>
                                        
                                        {{-- Mock UI for Hero --}}
                                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4 relative z-10">
                                            <div class="h-6 w-40 bg-zinc-100 dark:bg-zinc-800 rounded-md"></div>
                                            <div class="flex gap-3">
                                                <div class="h-10 w-10 bg-zinc-100 dark:bg-zinc-800 rounded-full"></div>
                                                <div class="h-10 w-28 bg-accent rounded-lg shadow-lg shadow-accent/30"></div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 relative z-10">
                                            <div class="h-28 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 flex flex-col justify-end group-hover:-translate-y-1 transition-transform duration-500">
                                                <div class="h-4 w-16 bg-zinc-200 dark:bg-zinc-700 rounded mb-2"></div>
                                                <div class="h-6 w-24 bg-zinc-300 dark:bg-zinc-600 rounded"></div>
                                            </div>
                                            <div class="h-28 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 flex flex-col justify-end group-hover:-translate-y-1 transition-transform duration-500 delay-75">
                                                <div class="h-4 w-16 bg-zinc-200 dark:bg-zinc-700 rounded mb-2"></div>
                                                <div class="h-6 w-24 bg-zinc-300 dark:bg-zinc-600 rounded"></div>
                                            </div>
                                            <div class="h-28 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 flex flex-col justify-end group-hover:-translate-y-1 transition-transform duration-500 delay-150">
                                                <div class="h-4 w-16 bg-zinc-200 dark:bg-zinc-700 rounded mb-2"></div>
                                                <div class="h-6 w-24 bg-zinc-300 dark:bg-zinc-600 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="space-y-4 relative z-10 mt-2">
                                            <div class="h-4 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full"></div>
                                            <div class="h-4 w-5/6 bg-zinc-100 dark:bg-zinc-800 rounded-full"></div>
                                            <div class="h-4 w-4/6 bg-zinc-100 dark:bg-zinc-800 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Features Section --}}
                <div id="features" class="relative bg-zinc-50 py-24 dark:bg-zinc-950 sm:py-32 overflow-hidden" x-data="{ shown: false }" x-intersect.half="shown = true">
                    <div class="absolute inset-0 bg-[url('/img/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))] dark:bg-[url('/img/grid-dark.svg')]"></div>
                    <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl lg:text-center" x-show="shown" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                            <flux:heading size="xl" class="text-4xl font-bold tracking-tight">Powerful Backend Features</flux:heading>
                            <flux:text class="mt-6 text-lg text-zinc-600 dark:text-zinc-400">
                                From tracking massive boulders to granular aggregates, Quarry Bill gives you total command over your supply chain, purchases, and sales.
                            </flux:text>
                        </div>
                        <div class="mx-auto mt-16 max-w-7xl sm:mt-20 lg:mt-24">
                            <dl class="grid max-w-xl grid-cols-1 gap-8 lg:max-w-none lg:grid-cols-3">
                                <!-- Feature 1 -->
                                <flux:card class="flex flex-col items-start gap-4 p-8 hover:-translate-y-2 hover:shadow-xl hover:shadow-accent/10 transition-all duration-300 border border-zinc-200 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm group">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                        <flux:icon name="truck" class="size-7" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold">Dual Operations Tracking</flux:heading>
                                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                                        Manage both <b>Dispatch (Sell)</b> and <b>Procurement (Buy)</b>. Create separate receipts, track suppliers vs. clients, and monitor separate dashboards for complete visibility.
                                    </flux:text>
                                </flux:card>

                                <!-- Feature 2 -->
                                <flux:card class="flex flex-col items-start gap-4 p-8 hover:-translate-y-2 hover:shadow-xl hover:shadow-accent/10 transition-all duration-300 border border-zinc-200 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm group">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground shadow-lg group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                                        <flux:icon name="chart-pie" class="size-7" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold">Advanced Analytics</flux:heading>
                                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                                        Live dashboards showing daily, weekly, and monthly volumes. Visual charts for <b>Dispatch Trends</b> and <b>Monthly Product Mix</b> help you make data-driven decisions.
                                    </flux:text>
                                </flux:card>

                                <!-- Feature 3 -->
                                <flux:card class="flex flex-col items-start gap-4 p-8 hover:-translate-y-2 hover:shadow-xl hover:shadow-accent/10 transition-all duration-300 border border-zinc-200 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm group">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                        <flux:icon name="document-text" class="size-7" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold">Automated PDF Generation</flux:heading>
                                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                                        Generate <b>professional PDF slips</b> and comprehensive <b>Daily/Monthly Reports</b> instantly. Ready to print, email, or archive with a single click.
                                    </flux:text>
                                </flux:card>

                                <!-- Feature 4 -->
                                <flux:card class="flex flex-col items-start gap-4 p-8 hover:-translate-y-2 hover:shadow-xl hover:shadow-accent/10 transition-all duration-300 border border-zinc-200 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm group">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground shadow-lg group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                                        <flux:icon name="users" class="size-7" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold">Entity Management</flux:heading>
                                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                                        Maintain dedicated registries for <b>Clients</b> and <b>Suppliers</b>. Track individual transaction histories, balances, and contact information securely.
                                    </flux:text>
                                </flux:card>

                                <!-- Feature 5 -->
                                <flux:card class="flex flex-col items-start gap-4 p-8 hover:-translate-y-2 hover:shadow-xl hover:shadow-accent/10 transition-all duration-300 border border-zinc-200 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm group">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                        <flux:icon name="tag" class="size-7" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold">Master Data Control</flux:heading>
                                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                                        Define unlimited <b>Material Types</b> (e.g., 20mm Aggregate, Stone Dust). Streamline data entry across all receipts with standardized product catalogs.
                                    </flux:text>
                                </flux:card>

                                <!-- Feature 6 -->
                                <flux:card class="flex flex-col items-start gap-4 p-8 hover:-translate-y-2 hover:shadow-xl hover:shadow-accent/10 transition-all duration-300 border border-zinc-200 dark:border-zinc-800/50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm group">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent text-accent-foreground shadow-lg group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                                        <flux:icon name="shield-check" class="size-7" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold">Secure Access</flux:heading>
                                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                                        Robust authentication with <b>Two-Factor Authentication (2FA)</b>, Passkey support, and secure session management. Your quarry data remains highly protected.
                                    </flux:text>
                                </flux:card>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- CTA Section --}}
                <div class="relative isolate mt-24 sm:mt-32">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8 pb-24">
                        <div class="relative isolate overflow-hidden bg-accent px-6 py-24 text-center shadow-2xl sm:rounded-3xl sm:px-16 group">
                            <div class="absolute -top-24 -right-24 size-96 bg-white/10 blur-3xl rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
                            <div class="absolute -bottom-24 -left-24 size-96 bg-black/10 blur-3xl rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
                            <flux:heading size="xl" class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-accent-foreground sm:text-4xl relative z-10">
                                Ready to digitize your quarry?
                            </flux:heading>
                            <flux:text class="mx-auto mt-6 max-w-xl text-lg leading-8 text-accent-foreground/80 relative z-10">
                                Experience the full power of Quarry Bill. Track your first load in minutes.
                            </flux:text>
                            <div class="mt-10 flex items-center justify-center gap-x-6 relative z-10">
                                @auth
                                    <flux:button href="{{ route('dashboard') }}" variant="primary" class="bg-accent-foreground text-accent hover:bg-accent-foreground/90 rounded-full px-8 py-3 font-bold transition-transform hover:scale-105">Go to Dashboard</flux:button>
                                @else
                                    <flux:button href="{{ route('login') }}" variant="primary" class="bg-accent-foreground text-accent hover:bg-accent-foreground/90 rounded-full px-8 py-3 font-bold transition-transform hover:scale-105">Log In Now</flux:button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="border-t border-zinc-200 bg-white py-12 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white">
                                <x-app-logo-icon class="size-5 text-white dark:text-zinc-900" />
                            </div>
                            <span class="text-lg font-bold tracking-tight">{{ config('app.name', 'Quarry Bill') }}</span>
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Quarry Bill') }}. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        @persist('flux-toast')
            <flux:toast />
        @endpersist
    </body>
</html>
