<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div class="space-y-6">
            <div>
                <flux:heading class="mb-2">{{ __('Mode') }}</flux:heading>
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                    <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                    <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
                </flux:radio.group>
            </div>

            <div x-data="{ 
                themeColor: localStorage.getItem('themeColor') || 'zinc',
                init() {
                    this.$watch('themeColor', value => {
                        document.documentElement.classList.forEach(className => {
                            if (className.startsWith('theme-')) {
                                document.documentElement.classList.remove(className);
                            }
                        });
                        if (value !== 'zinc') {
                            document.documentElement.classList.add('theme-' + value);
                        }
                        localStorage.setItem('themeColor', value);
                    })
                }
            }">
                <flux:heading class="mb-2">{{ __('Theme Color') }}</flux:heading>
                <flux:radio.group variant="segmented" x-model="themeColor">
                    <flux:radio value="zinc" icon="swatch">{{ __('Zinc') }}</flux:radio>
                    <flux:radio value="blue" icon="swatch">{{ __('Blue') }}</flux:radio>
                    <flux:radio value="red" icon="swatch">{{ __('Red') }}</flux:radio>
                    <flux:radio value="emerald" icon="swatch">{{ __('Emerald') }}</flux:radio>
                    <flux:radio value="amber" icon="swatch">{{ __('Amber') }}</flux:radio>
                </flux:radio.group>
            </div>
        </div>
    </x-pages::settings.layout>
</section>
