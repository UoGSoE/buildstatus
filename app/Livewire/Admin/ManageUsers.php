<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ManageUsers extends Component
{
    use WithPagination;

    public string $filter = '';

    public $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public bool $isAdmin = false;

    public function render()
    {
        return view('livewire.admin.manage-users', [
            'users' => $this->getUsers(),
        ]);
    }

    public function getUsers()
    {
        return User::query()
            ->when(strlen($this->filter) > 1, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->filter.'%')
                        ->orWhere('email', 'like', '%'.$this->filter.'%');
                });
            })
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(): void
    {
        $this->reset(['userId', 'name', 'email', 'password', 'isAdmin']);
        Flux::modal('user-form')->show();
    }

    public function edit($userId): void
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->isAdmin = $user->is_admin;
        Flux::modal('user-form')->show();
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email'.($this->userId ? ','.$this->userId : ''),
            'isAdmin' => 'boolean',
        ];

        if ($this->userId) {
            $rules['password'] = 'nullable|string|min:8';
        } else {
            $rules['password'] = 'required|string|min:8';
        }

        $validated = $this->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $validated['isAdmin'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($this->userId) {
            User::findOrFail($this->userId)->update($data);
            Flux::toast('User updated successfully');
        } else {
            User::create($data);
            Flux::toast('User created successfully');
        }

        $this->reset(['userId', 'name', 'email', 'password', 'isAdmin']);
        Flux::modal('user-form')->close();
    }

    public function delete($userId): void
    {
        $user = User::findOrFail($userId);
        $user->delete();
        Flux::toast('User deleted successfully');
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }
}
