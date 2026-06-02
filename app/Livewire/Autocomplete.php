<?php

namespace App\Livewire;

use Livewire\Attributes\Modelable;
use Livewire\Component;
use Flux\Flux;

class Autocomplete extends Component
{
    #[Modelable]
    public $value;

    public string $search = '';

    public string $model;

    public string $placeholder = '';

    public string $label = '';

    public string $labelClass = 'text-zinc-400';

    public bool $showDropdown = false;

    public string $selectedName = '';

    public function mount(string $model, string $placeholder = '', string $label = '', string $labelClass = 'text-zinc-400')
    {
        $this->model = $model;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->labelClass = $labelClass;

        if ($this->value) {
            $item = $this->model::find($this->value);
            if ($item) {
                $this->selectedName = $item->name;
                $this->search = $item->name;
            }
        }
    }

    public function updatedSearch()
    {
        if (empty($this->search)) {
            $this->value = null;
            $this->showDropdown = false;

            return;
        }

        $this->showDropdown = true;
    }

    public function updatedValue($value)
    {
        if ($value) {
            $item = $this->model::find($value);
            if ($item) {
                $this->selectedName = $item->name;
                $this->search = $item->name;
            }
        } else {
            $this->selectedName = '';
            $this->search = '';
        }
    }

    public function selectItem($id, $name)
    {
        $this->value = $id;
        $this->search = $name;
        $this->selectedName = $name;
        $this->showDropdown = false;
        
        Flux::toast(__('Selected ') . $name);
    }

    public function clear()
    {
        $this->value = null;
        $this->search = '';
        $this->selectedName = '';
        $this->showDropdown = false;
    }

    public function render()
    {
        $results = [];
        if (strlen($this->search) >= 1) {
            $results = $this->model::where('name', 'like', '%'.$this->search.'%')
                ->limit(10)
                ->get();
        }

        return view('livewire.autocomplete', [
            'results' => $results,
        ]);
    }
}
