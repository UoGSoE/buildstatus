<?php

use App\Livewire\Profile;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('component can render', function () {
    Livewire::test(Profile::class)
        ->assertStatus(200);
});

test('displays profile heading', function () {
    Livewire::test(Profile::class)
        ->assertSee('Profile');
});

test('displays profile description', function () {
    Livewire::test(Profile::class)
        ->assertSee('Manage your account settings and API tokens');
});

test('includes api keys component', function () {
    Livewire::test(Profile::class)
        ->assertSeeLivewire('api-keys');
});

test('profile route is accessible', function () {
    $this->get('/profile')
        ->assertSeeLivewire(Profile::class)
        ->assertStatus(200);
});

test('profile route requires authentication', function () {
    auth()->logout();

    $this->get('/profile')
        ->assertRedirect();
});
