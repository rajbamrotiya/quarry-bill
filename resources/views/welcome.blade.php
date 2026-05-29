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
                            <flux:icon name="truck" variant="mini" class="text-white dark:text-zinc-900" />
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
                <div class="relative isolate overflow-hidden">
                    <div class="mx-auto max-w-7xl px-6 pt-10 pb-24 sm:pb-32 lg:flex lg:px-8 lg:py-40">
                        <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl lg:flex-shrink-0 lg:pt-8">
                            <flux:heading size="xl" level="1" class="text-4xl font-bold tracking-tight sm:text-6xl">
                                Streamline Your Quarry Operations
                            </flux:heading>
                            <flux:text class="mt-6 text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                                Efficient receipt management, real-time client tracking, and automated reporting designed specifically for the quarry industry.
                            </flux:text>
                            <div class="mt-10 flex items-center gap-x-6">
                                @auth
                                    <flux:button href="{{ route('dashboard') }}" variant="primary">Go to Dashboard</flux:button>
                                @else
                                    <flux:button href="{{ route('login') }}" variant="primary">Get Started</flux:button>
                                @endauth
                                <flux:button href="#features" variant="ghost" icon-trailing="arrow-down">Learn more</flux:button>
                            </div>
                        </div>
                        <div class="mx-auto mt-16 flex max-w-2xl sm:mt-24 lg:ml-10 lg:mr-0 lg:mt-0 lg:max-w-none lg:flex-none xl:ml-32">
                            <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none">
                                <div class="rounded-2xl bg-zinc-900/5 p-2 ring-1 ring-inset ring-zinc-900/10 dark:bg-white/5 dark:ring-white/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                                    <div class="w-[30rem] lg:w-[40rem] aspect-[16/10] bg-white dark:bg-zinc-800 rounded-lg shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-700 p-8 flex flex-col gap-6">
                                        {{-- Mock UI for Hero --}}
                                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-700 pb-4">
                                            <div class="h-4 w-32 bg-zinc-100 dark:bg-zinc-700 rounded-full"></div>
                                            <div class="flex gap-2">
                                                <div class="h-8 w-8 bg-zinc-100 dark:bg-zinc-700 rounded-full"></div>
                                                <div class="h-8 w-24 bg-zinc-900 dark:bg-white rounded-lg"></div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="h-24 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700"></div>
                                            <div class="h-24 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700"></div>
                                            <div class="h-24 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700"></div>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-700 rounded-full"></div>
                                            <div class="h-3 w-5/6 bg-zinc-100 dark:bg-zinc-700 rounded-full"></div>
                                            <div class="h-3 w-4/6 bg-zinc-100 dark:bg-zinc-700 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Features Section --}}
                <div id="features" class="bg-zinc-50 py-24 dark:bg-zinc-950 sm:py-32">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl lg:text-center">
                            <flux:heading size="xl">Everything you need to manage your quarry</flux:heading>
                            <flux:text class="mt-6 text-lg text-zinc-600 dark:text-zinc-400">
                                No more manual calculations or paper mess. Digitally track every load leaving your quarry.
                            </flux:text>
                        </div>
                        <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                            <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                                <flux:card class="flex flex-col items-start gap-4 p-8">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                                        <flux:icon name="document-text" class="size-6" />
                                    </div>
                                    <flux:heading size="lg">Receipt Management</flux:heading>
                                    <flux:text>
                                        Easily create and manage digital receipts with automatic net weight calculations and payment type tracking.
                                    </flux:text>
                                </flux:card>

                                <flux:card class="flex flex-col items-start gap-4 p-8">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                                        <flux:icon name="users" class="size-6" />
                                    </div>
                                    <flux:heading size="lg">Client Database</flux:heading>
                                    <flux:text>
                                        Maintain a detailed directory of your clients and their transaction history for better relationship management.
                                    </flux:text>
                                </flux:card>

                                <flux:card class="flex flex-col items-start gap-4 p-8">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                                        <flux:icon name="chart-bar" class="size-6" />
                                    </div>
                                    <flux:heading size="lg">Automated Reporting</flux:heading>
                                    <flux:text>
                                        Generate daily summary reports and professional PDF receipts with a single click, ready for your records.
                                    </flux:text>
                                </flux:card>
                            </dl>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="border-t border-zinc-200 bg-white py-12 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900 dark:bg-white">
                                <flux:icon name="truck" variant="mini" class="text-white dark:text-zinc-900" />
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
