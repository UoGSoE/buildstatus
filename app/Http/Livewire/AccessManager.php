<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AccessManager extends Component
{
    public $showCreateForm = false;

    public $userError = '';
    public $username = '';
    public $email = '';
    public $surname = '';
    public $forenames = '';
    public $description = '';

    public function render()
    {
        return view('livewire.access-manager', [
            'users' => User::orderBy('surname')->get(),
        ]);
    }

    public function deleteUser($userId)
    {
        if ($userId == auth()->id()) {
            return;
        }

        User::find($userId)->delete();
    }

    public function lookupUser()
    {
        if (! $this->username) {
            return;
        }

        $ldapUser = \Ohffs\Ldap\LdapFacade::findUser($this->username);
        if (! $ldapUser) {
            $this->userError = 'Could not find that user';
        }

        $this->userError = '';
        $this->username = $ldapUser->username;
        $this->email = $ldapUser->email;
        $this->surname = $ldapUser->surname;
        $this->forenames = $ldapUser->forenames;
    }

    public function createUser()
    {
        Validator::make([
            'username' => $this->username,
            'email' => $this->email,
            'surname' => $this->surname,
            'forenames' => $this->forenames,
            'description' => $this->description,
        ], [
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'surname' => 'required',
            'forenames' => 'required',
            'description' => 'nullable|string|max:200',
        ])->validate();

        User::create([
            'username' => $this->username,
            'email' => $this->email,
            'surname' => $this->surname,
            'forenames' => $this->forenames,
            'description' => $this->description,
            'is_staff' => true,
            'password' => Str::random(64),
        ]);

        $this->reset();
    }
}
