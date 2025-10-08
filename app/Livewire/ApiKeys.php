<?php

namespace App\Livewire;

use Flux\Flux;
use Livewire\Component;

class ApiKeys extends Component
{
    public string $tokenName = '';

    public ?string $plaintextToken = null;

    public function render()
    {
        return view('livewire.api-keys', [
            'tokens' => auth()->user()->tokens()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['tokenName', 'plaintextToken']);
        Flux::modal('token-form')->show();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'tokenName' => 'required|string|max:255',
        ]);

        $token = auth()->user()->createToken($validated['tokenName']);

        $this->plaintextToken = $token->plainTextToken;
        $this->tokenName = '';

        Flux::modal('token-form')->close();
        Flux::modal('token-display')->show();
        Flux::toast('API token created successfully');
    }

    public function revoke($tokenId): void
    {
        $token = auth()->user()->tokens()->where('id', $tokenId)->first();

        if (! $token) {
            Flux::toast('Token not found', variant: 'danger');

            return;
        }

        $token->delete();
        Flux::toast('API token revoked successfully');
    }

    public function closeTokenDisplay(): void
    {
        $this->plaintextToken = null;
        Flux::modal('token-display')->close();
    }
}
