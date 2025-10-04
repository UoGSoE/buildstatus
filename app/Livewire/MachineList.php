<?php

namespace App\Livewire;

use App\Models\Lab;
use App\Models\Machine;
use Livewire\Component;
use Livewire\WithPagination;

class MachineList extends Component
{
    use WithPagination;

    public $labId;

    public string $filter = '';

    public bool $autoRefresh = true;

    public function mount($labId = null)
    {
        $this->labId = $labId;
    }

    public function render()
    {
        return view('livewire.machine-list', [
            'machines' => $this->getMachines(),
            'labs' => Lab::orderBy('name')->get(),
        ]);
    }

    public function getMachines()
    {
        $filter = strtolower(trim($this->filter));
        return Machine::with(['lab', 'logs'])
            ->when(strlen($filter) > 1, function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter . '%')
                    ->orWhere('ip_address', 'like', '%' . $filter . '%')
                    ->orWhere('status', 'like', '%' . $filter . '%');
            })
            ->when($this->labId, function ($query) {
                $query->where('lab_id', $this->labId);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(100);
    }
}
