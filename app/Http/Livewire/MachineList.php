<?php

namespace App\Http\Livewire;

use App\Models\Machine;
use Livewire\Component;

class MachineList extends Component
{
    public function render()
    {
        return view('livewire.machine-list', [
            'machines' => Machine::latest('updated_at')->get(),
        ]);
    }
}
