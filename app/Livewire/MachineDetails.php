<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Machine;
use Livewire\WithPagination;

class MachineDetails extends Component
{
    use WithPagination;

    public $machine;

    public function mount(Machine $machine)
    {
        $this->machine = $machine;
    }

    public function render()
    {
        return view('livewire.machine-details', [
            'logs' => $this->machine->logs()->latest()->paginate(50),
        ]);
    }
}
