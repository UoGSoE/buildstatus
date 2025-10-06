<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Machine;

class MachineDetails extends Component
{
    public $machine;

    public function mount(Machine $machine)
    {
        $this->machine = $machine;
    }

    public function render()
    {
        return view('livewire.machine-details');
    }
}
