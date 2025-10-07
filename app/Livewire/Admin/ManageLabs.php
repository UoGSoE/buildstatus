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
        $lab->delete();
        Flux::toast('Lab deleted successfully');
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }
}
