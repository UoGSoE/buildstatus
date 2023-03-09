<?php

namespace App\Http\Livewire;

use App\Models\Machine;
use App\Models\Tag;
use Livewire\Component;

class MachineList extends Component
{
    public $password = '';

    public $search = '';

    public $tags = [];

    public $showTagList = false;

    protected $queryString = ['tags'];

    public function render()
    {
        return view('livewire.machine-list', [
            'machines' => $this->getMachines(),
            'availableTags' => Tag::orderBy('name')->get(),
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
            ->when(count($this->tags) > 0, function ($query) {
                foreach ($this->tags as $tagId) {
                    $query->whereHas('tags', function ($query) use ($tagId) {
                        $query->where('tag_id', $tagId);
                    });
                }
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

        $this->getMachines()->each->delete();

        $this->password = '';
    }
}
