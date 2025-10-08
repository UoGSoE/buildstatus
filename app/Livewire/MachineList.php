<?php

namespace App\Livewire;

use App\Models\Lab;
use App\Models\Machine;
use Flux\Flux;
use Livewire\Attributes\Url;
use Livewire\Component;
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

    public $bulkDeleteCount = 0;

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
                $query->where(fn ($query) => $query->where('name', 'like', '%'.$filter.'%')
                    ->orWhere('ip_address', 'like', '%'.$filter.'%')
                    ->orWhere('status', 'like', '%'.$filter.'%'));
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

        // load 10 latest logs
        $this->machineDetails->logs = $this->machineDetails->logs()->latest()->limit(10)->get();

        Flux::modal('machine-details')->show();
    }

    public function confirmBulkDelete(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        // Get the count of machines that will be deleted
        $this->bulkDeleteCount = $this->getMachinesQuery()->count();

        if ($this->bulkDeleteCount === 0) {
            Flux::toast('No machines match the current filter', variant: 'warning');

            return;
        }

        Flux::modal('bulk-delete-confirmation')->show();
    }

    public function bulkDelete(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $count = $this->getMachinesQuery()->count();
        $this->getMachinesQuery()->delete();

        $this->reset(['bulkDeleteCount']);
        Flux::modal('bulk-delete-confirmation')->close();
        Flux::toast("{$count} ".\Illuminate\Support\Str::plural('machine', $count).' deleted successfully');
    }

    protected function getMachinesQuery()
    {
        $filter = strtolower(trim($this->filter));

        return Machine::query()
            ->when(strlen($filter) > 1, function ($query) use ($filter) {
                $query->where(fn ($query) => $query->where('name', 'like', '%'.$filter.'%')
                    ->orWhere('ip_address', 'like', '%'.$filter.'%')
                    ->orWhere('status', 'like', '%'.$filter.'%'));
            })
            ->when($this->labId, function ($query) {
                $query->where('lab_id', $this->labId);
            });
    }

    public function updatedLabId(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }
}
