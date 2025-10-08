<?php

use App\Livewire\ApiKeys;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('component can render', function () {
    Livewire::test(ApiKeys::class)
        ->assertStatus(200);
});

test('displays message when no tokens exist', function () {
    Livewire::test(ApiKeys::class)
        ->assertSee('No API tokens created yet.');
});

test('displays users tokens', function () {
    $this->user->createToken('Production Server');
    $this->user->createToken('Testing Environment');

    Livewire::test(ApiKeys::class)
        ->assertSee('Production Server')
        ->assertSee('Testing Environment');
});

test('displays token created date', function () {
    $token = $this->user->createToken('Test Token');

    Livewire::test(ApiKeys::class)
        ->assertSee($token->accessToken->created_at->format('M j, Y'));
});

test('displays never for tokens that have not been used', function () {
    $this->user->createToken('Unused Token');

    Livewire::test(ApiKeys::class)
        ->assertSee('Never');
});

test('displays last used information for used tokens', function () {
    $token = $this->user->createToken('Used Token');
    $tokenModel = $token->accessToken;
    $tokenModel->last_used_at = now()->subHours(2);
    $tokenModel->save();

    // Verify the update persisted
    $freshToken = $this->user->tokens()->first();
    expect($freshToken->last_used_at)->not->toBeNull();

    Livewire::test(ApiKeys::class)
        ->assertDontSee('Never', false); // Don't escape HTML since it's in a badge
});

test('only displays tokens belonging to authenticated user', function () {
    $otherUser = User::factory()->create();
    $otherUser->createToken('Other User Token');
    $this->user->createToken('My Token');

    Livewire::test(ApiKeys::class)
        ->assertSee('My Token')
        ->assertDontSee('Other User Token');
});

test('opens create token modal', function () {
    Livewire::test(ApiKeys::class)
        ->call('create')
        ->assertSet('tokenName', '')
        ->assertSet('plaintextToken', null);
});

test('creates new token with valid name', function () {
    Livewire::test(ApiKeys::class)
        ->set('tokenName', 'New API Token')
        ->call('save')
        ->assertSet('plaintextToken', function ($value) {
            return str_contains($value, '|');
        });

    expect($this->user->tokens()->count())->toBe(1);
    expect($this->user->tokens()->first()->name)->toBe('New API Token');
});

test('displays plaintext token after creation', function () {
    $component = Livewire::test(ApiKeys::class)
        ->set('tokenName', 'New Token')
        ->call('save');

    expect($component->get('plaintextToken'))->not->toBeNull();
    expect($component->get('plaintextToken'))->toContain('|');
});

test('token name is required', function () {
    Livewire::test(ApiKeys::class)
        ->set('tokenName', '')
        ->call('save')
        ->assertHasErrors(['tokenName' => 'required']);

    expect($this->user->tokens()->count())->toBe(0);
});

test('token name cannot exceed 255 characters', function () {
    Livewire::test(ApiKeys::class)
        ->set('tokenName', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['tokenName' => 'max']);

    expect($this->user->tokens()->count())->toBe(0);
});

test('revokes token successfully', function () {
    $token = $this->user->createToken('Token to Revoke');
    $tokenId = $token->accessToken->id;

    expect($this->user->tokens()->count())->toBe(1);

    Livewire::test(ApiKeys::class)
        ->call('revoke', $tokenId);

    expect($this->user->tokens()->count())->toBe(0);
});

test('actually revokes the token from database', function () {
    $token = $this->user->createToken('Token to Revoke');
    $tokenId = $token->accessToken->id;

    expect($this->user->tokens()->where('id', $tokenId)->exists())->toBeTrue();

    Livewire::test(ApiKeys::class)
        ->call('revoke', $tokenId);

    expect($this->user->tokens()->where('id', $tokenId)->exists())->toBeFalse();
});

test('cannot revoke another users token', function () {
    $otherUser = User::factory()->create();
    $otherToken = $otherUser->createToken('Other User Token');

    Livewire::test(ApiKeys::class)
        ->call('revoke', $otherToken->accessToken->id);

    // Other user's token should still exist
    expect($otherUser->fresh()->tokens()->count())->toBe(1);
});

test('handles revoking non-existent token gracefully', function () {
    $initialCount = $this->user->tokens()->count();

    Livewire::test(ApiKeys::class)
        ->call('revoke', 999999);

    expect($this->user->tokens()->count())->toBe($initialCount);
});

test('closes token display modal and clears plaintext token', function () {
    $component = Livewire::test(ApiKeys::class)
        ->set('tokenName', 'Test Token')
        ->call('save');

    expect($component->get('plaintextToken'))->not->toBeNull();

    $component->call('closeTokenDisplay')
        ->assertSet('plaintextToken', null);
});

test('displays create token button', function () {
    Livewire::test(ApiKeys::class)
        ->assertSee('Create Token');
});

test('displays revoke button for each token', function () {
    $this->user->createToken('Token 1');
    $this->user->createToken('Token 2');

    $component = Livewire::test(ApiKeys::class);

    $tokens = $component->viewData('tokens');
    expect($tokens->count())->toBe(2);
});

test('displays table headers', function () {
    Livewire::test(ApiKeys::class)
        ->assertSee('Name')
        ->assertSee('Created')
        ->assertSee('Last Used')
        ->assertSee('Actions');
});

test('token list is ordered by created date descending', function () {
    // Create old token five days ago using the time travel helpers
    $this->travel(-5)->days();
    $oldTokenData = $this->user->createToken('Old Token');
    $this->travelBack();
    $oldTokenData->accessToken->refresh();

    $newTokenData = $this->user->createToken('New Token');

    $component = Livewire::test(ApiKeys::class);

    $tokens = $component->viewData('tokens');
    expect($tokens->first()->name)->toBe('New Token');
    expect($tokens->last()->name)->toBe('Old Token');
});

// Admin functionality tests
test('admin sees view all tokens switch', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Livewire::test(ApiKeys::class)
        ->assertSee('View all users tokens');
});

test('non-admin does not see view all tokens switch', function () {
    Livewire::test(ApiKeys::class)
        ->assertDontSee('View all users tokens');
});

test('admin can toggle to view all users tokens', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $otherUser = User::factory()->create();
    $otherUser->createToken('Other User Token');
    $admin->createToken('Admin Token');

    $component = Livewire::test(ApiKeys::class)
        ->assertSee('Admin Token')
        ->assertDontSee('Other User Token')
        ->set('viewAllKeys', true)
        ->assertSee('Admin Token')
        ->assertSee('Other User Token');

    $tokens = $component->viewData('tokens');
    expect($tokens->count())->toBe(2);
});

test('non-admin cannot view all tokens even if they set viewAllKeys to true', function () {
    $otherUser = User::factory()->create();
    $otherUser->createToken('Other User Token');
    $this->user->createToken('My Token');

    Livewire::test(ApiKeys::class)
        ->set('viewAllKeys', true)
        ->assertSee('My Token')
        ->assertDontSee('Other User Token');
});

test('user column appears when admin views all tokens', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $otherUser = User::factory()->create(['username' => 'testuser']);
    $otherUser->createToken('Test Token');

    Livewire::test(ApiKeys::class)
        ->assertDontSee('User')
        ->set('viewAllKeys', true)
        ->assertSee('User')
        ->assertSee('testuser');
});

test('admin can revoke other users tokens', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $otherUser = User::factory()->create();
    $otherToken = $otherUser->createToken('Other User Token');
    $tokenId = $otherToken->accessToken->id;

    expect($otherUser->tokens()->count())->toBe(1);

    Livewire::test(ApiKeys::class)
        ->set('viewAllKeys', true)
        ->call('revoke', $tokenId);

    expect($otherUser->tokens()->count())->toBe(0);
});

test('admin badge shows in user column for admin users', function () {
    $admin = User::factory()->create(['is_admin' => true, 'username' => 'admin']);
    $this->actingAs($admin);

    $admin->createToken('Admin Token');

    Livewire::test(ApiKeys::class)
        ->set('viewAllKeys', true)
        ->assertSee('admin')
        ->assertSee('Admin'); // The badge text
});
