<?php

namespace App\Livewire;

use Flux\Flux;
use App\Models\Lab;
use App\Models\Machine;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class MachineList extends Component
{
    use WithPagination;

    #[Url(as: 'lab_id', except: '')]
    public $labId = '';

    #[Url(except: '')]
    public string $filter = '';

    public bool $autoRefresh = true;

    public $machineDetails;

    public function mount($labId = '')
    {
        if ($labId) {
            $this->labId = $labId;
        }
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
        return Machine::with(['lab', 'logs' => fn ($query) => $query->latest()->limit(10)])
            ->when(strlen($filter) > 1, function ($query) use ($filter) {
                $query->where(fn ($query) => $query->where('name', 'like', '%' . $filter . '%')
                                                ->orWhere('ip_address', 'like', '%' . $filter . '%')
                                                ->orWhere('status', 'like', '%' . $filter . '%'));
            })
            ->when($this->labId, function ($query) {
                $query->where('lab_id', $this->labId);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(100);
    }

    public function showMachineDetails($machineId)
    {
        $this->machineDetails = Machine::findOrFail($machineId);

        Flux::modal('machine-details')->show();
    }
}
