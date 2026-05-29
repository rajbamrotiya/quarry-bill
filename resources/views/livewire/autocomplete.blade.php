<div>
    <div class="relative" x-data="{ open: @entangle('showDropdown') }" @click.away="open = false">
        <flux:field>
            @if($label)
                <flux:label class="uppercase text-[10px] font-bold text-zinc-400 mb-2 tracking-tight">{{ $label }}</flux:label>
            @endif
            
            <div class="relative">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    :placeholder="$placeholder"
                    autocomplete="off"
                    @focus="open = true"
                    class="pr-10"
                />
                
                @if($value)
                    <button type="button" wire:click="clear" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 focus:outline-none">
                        <flux:icon name="x-mark" class="size-4" />
                    </button>
                @endif
            </div>
        </flux:field>

        <div 
            x-show="open && $wire.search.length >= 1" 
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl overflow-hidden"
            style="display: none;"
        >
            @if(count($results) > 0)
                <div class="py-1">
                    @foreach($results as $result)
                        <button 
                            type="button"
                            wire:click="selectItem('{{ $result->id }}', '{{ addslashes($result->name) }}')"
                            class="w-full text-left px-4 py-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none transition-colors duration-150 group"
                        >
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200 group-hover:text-zinc-900 dark:group-hover:text-white">{{ $result->name }}</span>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-3 text-center">
                    <span class="text-sm text-zinc-500 italic">{{ __('No results found for') }} "{{ $search }}"</span>
                </div>
            @endif
        </div>
    </div>
</div>
