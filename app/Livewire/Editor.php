<?php

namespace App\Livewire;

use Livewire\Component;

class Editor extends Component
{
    const EVENT_VALUE_UPDATED = 'trix_value_updated';
    public $value;
    public $trixId;

    public function mount($value = '')
    {
        $this->value = $value;
        $this->trixId = 'trix-' . uniqid();
    }

    public function updatedValue($value)
    {
        $this->dispatch(self::EVENT_VALUE_UPDATED, value: $value);
    }

    public function render()
    {
        return view('livewire.editor');
    }
}
