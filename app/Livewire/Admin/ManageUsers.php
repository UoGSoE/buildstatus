<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ManageUsers extends Component
{
    use WithPagination;

    public string $filter = '';

    public $userId = null;

    public string $username = '';

    public string $surname = '';

    public string $forenames = '';

    public string $email = '';

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
                    $q->where('surname', 'like', '%'.$this->filter.'%')
                        ->orWhere('forenames', 'like', '%'.$this->filter.'%')
                        ->orWhere('username', 'like', '%'.$this->filter.'%')
                        ->orWhere('email', 'like', '%'.$this->filter.'%');
                });
            })
            ->orderBy('surname')
            ->paginate(20);
    }

    public function create(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $this->reset(['userId', 'username', 'surname', 'forenames', 'email', 'isAdmin']);
        Flux::modal('user-form')->show();
    }

    public function edit($userId): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->username = $user->username;
        $this->surname = $user->surname;
        $this->forenames = $user->forenames;
        $this->email = $user->email;
        $this->isAdmin = $user->is_admin;
        Flux::modal('user-form')->show();
    }

    public function save(): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        $validated = $this->validate([
            'username' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'forenames' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email'.($this->userId ? ','.$this->userId : ''),
            'isAdmin' => 'boolean',
        ]);

        $data = [
            'username' => $validated['username'],
            'surname' => $validated['surname'],
            'forenames' => $validated['forenames'],
            'email' => $validated['email'],
        ];

        // Don't allow users to change their own admin status
        if ($this->userId !== auth()->user()->id) {
            $data['is_admin'] = $validated['isAdmin'];
        }

        if ($this->userId) {
            User::findOrFail($this->userId)->update($data);
            Flux::toast('User updated successfully');
        } else {
            // Auto-generate a strong random password for new users
            $data['password'] = Hash::make(Str::random(32));
            User::create($data);
            Flux::toast('User created successfully');
        }

        $this->reset(['userId', 'username', 'surname', 'forenames', 'email', 'isAdmin']);
        Flux::modal('user-form')->close();
    }

    public function toggleAdmin($userId): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        if (auth()->user()->id === $userId) {
            Flux::toast('You cannot change your own admin status', variant: 'danger');

            return;
        }

        $user = User::findOrFail($userId);
        $user->update(['is_admin' => ! $user->is_admin]);
        Flux::toast($user->is_admin ? 'User promoted to admin' : 'Admin privileges revoked');
    }

    public function delete($userId): void
    {
        if (! auth()->user()->isAdmin()) {
            Flux::toast('Unauthorized action', variant: 'danger');

            return;
        }

        if (auth()->user()->id === $userId) {
            Flux::toast('You cannot delete your own account', variant: 'danger');

            return;
        }

        $user = User::findOrFail($userId);
        $user->delete();
        Flux::toast('User deleted successfully');
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }
}
