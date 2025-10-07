<?php

namespace App\Livewire\Admin;

use App\Models\Lab;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class ManageLabs extends Component
{
    use WithPagination;

    public string $filter = '';

    public $labId = null;

    public string $name = '';

    public string $notes = '';

    public $labToDelete = null;

    public $reassignLabId = '';

    public function render()
    {
        return view('livewire.admin.manage-labs', [
            'labs' => $this->getLabs(),
        ]);
    }

    public function getLabs()
    {
        return Lab::query()
            ->when(strlen($this->filter) > 1, function ($query) {
                $query->where('name', 'like', '%'.$this->filter.'%');
            })
            ->withCount('machines')
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $this->reset(['labId', 'name', 'notes']);
        Flux::modal('lab-form')->show();
    }

    public function edit($labId): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $lab = Lab::findOrFail($labId);
        $this->labId = $lab->id;
        $this->name = $lab->name;
        $this->notes = $lab->notes ?? '';
        Flux::modal('lab-form')->show();
    }

    public function save(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($this->labId) {
            Lab::findOrFail($this->labId)->update($validated);
            Flux::toast('Lab updated successfully');
        } else {
            Lab::create($validated);
            Flux::toast('Lab created successfully');
        }

        $this->reset(['labId', 'name', 'notes']);
        Flux::modal('lab-form')->close();
    }

    public function delete($labId): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $lab = Lab::findOrFail($labId);

        // If lab has machines, show confirmation modal
        if ($lab->machines()->count() > 0) {
            $this->labToDelete = $lab;
            $this->reassignLabId = '';
            Flux::modal('delete-lab-confirmation')->show();

            return;
        }

        // No machines, delete directly
        $lab->delete();
        Flux::toast('Lab deleted successfully');
    }

    public function confirmDeleteWithReassign(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        if (! $this->labToDelete) {
            return;
        }

        if (empty($this->reassignLabId)) {
            Flux::toast('Please select a lab to reassign machines to', variant: 'danger');

            return;
        }

        // Reassign all machines to the new lab
        $this->labToDelete->machines()->update(['lab_id' => $this->reassignLabId]);

        // Delete the lab
        $labName = $this->labToDelete->name;
        $this->labToDelete->delete();

        $this->reset(['labToDelete', 'reassignLabId']);
        Flux::modal('delete-lab-confirmation')->close();
        Flux::toast("Lab '{$labName}' deleted and machines reassigned successfully");
    }

    public function confirmDeleteWithMachines(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        if (! $this->labToDelete) {
            return;
        }

        // Delete all machines first, then delete the lab
        $machineCount = $this->labToDelete->machines()->count();
        $this->labToDelete->machines()->delete();

        $labName = $this->labToDelete->name;
        $this->labToDelete->delete();

        $this->reset(['labToDelete', 'reassignLabId']);
        Flux::modal('delete-lab-confirmation')->close();
        Flux::toast("Lab '{$labName}' and {$machineCount} machine(s) deleted successfully");
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }
}
