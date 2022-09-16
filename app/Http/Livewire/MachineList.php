<?php

namespace App\Http\Livewire;

use App\Models\Machine;
use Livewire\Component;

class MachineList extends Component
{
    public $password = '';
    public $search = '';

    public function render()
    {
        return view('livewire.machine-list', [
            'machines' => $this->getMachines(),
        ]);
    }

    public function getMachines()
    {
        $search = trim($this->search);
        return Machine::latest('updated_at')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            })
            ->get();
    }

    public function markComplete($machineId)
    {
        $machine = Machine::findOrFail($machineId);
        $machine->markAsComplete();
    }

    public function deleteMachine($machineId)
    {
        $machine = Machine::findOrFail($machineId);
        $machine->delete();
    }

    public function search()
    {
        // placeholder to trigger on a wire:click
    }

    public function truncateMachines()
    {
        if ($this->password !== config('buildstatus.admin_password')) {
            return;
        }

        Machine::truncate();

        $this->password = '';
    }
}
