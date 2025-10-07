<?php

use App\Livewire\Admin\ManageUsers;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
});

test('component can render', function () {
    Livewire::test(ManageUsers::class)
        ->assertStatus(200);
});

test('displays users with details', function () {
    User::factory()->create([
        'username' => 'jdoe',
        'forenames' => 'John',
        'surname' => 'Doe',
        'email' => 'jdoe@example.com',
        'is_admin' => true,
    ]);

    Livewire::test(ManageUsers::class)
        ->assertSee('jdoe')
        ->assertSee('John Doe')
        ->assertSee('jdoe@example.com')
        ->assertSee('Yes');
});

test('filters users by surname', function () {
    User::factory()->create(['surname' => 'Smith', 'forenames' => 'Alice', 'username' => 'asmith']);
    User::factory()->create(['surname' => 'Jones', 'forenames' => 'Bob', 'username' => 'bjones']);

    Livewire::test(ManageUsers::class)
        ->set('filter', 'smith')
        ->assertSee('asmith')
        ->assertDontSee('bjones');
});

test('filters users by forenames', function () {
    User::factory()->create(['surname' => 'Smith', 'forenames' => 'Alice', 'username' => 'asmith']);
    User::factory()->create(['surname' => 'Jones', 'forenames' => 'Bob', 'username' => 'bjones']);

    Livewire::test(ManageUsers::class)
        ->set('filter', 'alice')
        ->assertSee('asmith')
        ->assertDontSee('bjones');
});

test('filters users by username', function () {
    User::factory()->create(['surname' => 'Smith', 'forenames' => 'Alice', 'username' => 'asmith']);
    User::factory()->create(['surname' => 'Jones', 'forenames' => 'Bob', 'username' => 'bjones']);

    Livewire::test(ManageUsers::class)
        ->set('filter', 'asmith')
        ->assertSee('asmith')
        ->assertDontSee('bjones');
});

test('filters users by email', function () {
    User::factory()->create(['email' => 'alice@example.com', 'username' => 'asmith']);
    User::factory()->create(['email' => 'bob@example.com', 'username' => 'bjones']);

    Livewire::test(ManageUsers::class)
        ->set('filter', 'alice@')
        ->assertSee('asmith')
        ->assertDontSee('bjones');
});

test('shows all users when filter is empty', function () {
    User::factory()->create(['username' => 'asmith']);
    User::factory()->create(['username' => 'bjones']);

    Livewire::test(ManageUsers::class)
        ->set('filter', '')
        ->assertSee('asmith')
        ->assertSee('bjones');
});

test('only applies filter when more than 1 character', function () {
    User::factory()->create(['username' => 'asmith']);
    User::factory()->create(['username' => 'bjones']);

    Livewire::test(ManageUsers::class)
        ->set('filter', 'a')
        ->assertSee('asmith')
        ->assertSee('bjones');
});

test('orders users by surname', function () {
    User::factory()->create(['surname' => 'Zeta', 'username' => 'zzeta']);
    User::factory()->create(['surname' => 'Alpha', 'username' => 'aalpha']);

    $component = Livewire::test(ManageUsers::class);

    $users = $component->viewData('users');
    expect($users->first()->surname)->toBe('Alpha');
});

test('displays users with pagination', function () {
    User::factory()->count(25)->create();

    Livewire::test(ManageUsers::class)
        ->assertSeeHtml('data-flux-pagination');
});

test('can open create modal', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->assertSet('userId', null)
        ->assertSet('username', '')
        ->assertSet('surname', '')
        ->assertSet('forenames', '')
        ->assertSet('email', '')
        ->assertSet('password', '')
        ->assertSet('isAdmin', false);
});

test('can create a new user', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'newuser')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'newuser@example.com')
        ->set('password', 'password123')
        ->set('isAdmin', true)
        ->call('save');

    $user = User::where('username', 'newuser')->first();
    expect($user)->not->toBeNull();
    expect($user->surname)->toBe('Smith');
    expect($user->forenames)->toBe('John');
    expect($user->email)->toBe('newuser@example.com');
    expect($user->is_admin)->toBeTrue();
});

test('validates username is required when creating', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', '')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasErrors(['username' => 'required']);
});

test('validates surname is required when creating', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'jsmith')
        ->set('surname', '')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasErrors(['surname' => 'required']);
});

test('validates forenames is required when creating', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'jsmith')
        ->set('surname', 'Smith')
        ->set('forenames', '')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasErrors(['forenames' => 'required']);
});

test('validates email is required when creating', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'jsmith')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', '')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasErrors(['email' => 'required']);
});

test('validates email must be valid format', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'jsmith')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'not-an-email')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasErrors(['email' => 'email']);
});

test('validates email must be unique when creating', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'newuser')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});

test('validates password is required when creating', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'jsmith')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->set('password', '')
        ->call('save')
        ->assertHasErrors(['password' => 'required']);
});

test('validates password minimum length when creating', function () {
    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'jsmith')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->set('password', 'short')
        ->call('save')
        ->assertHasErrors(['password' => 'min']);
});

test('can open edit modal with user data', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'surname' => 'Smith',
        'forenames' => 'John',
        'email' => 'test@example.com',
        'is_admin' => true,
    ]);

    Livewire::test(ManageUsers::class)
        ->call('edit', $user->id)
        ->assertSet('userId', $user->id)
        ->assertSet('username', 'testuser')
        ->assertSet('surname', 'Smith')
        ->assertSet('forenames', 'John')
        ->assertSet('email', 'test@example.com')
        ->assertSet('isAdmin', true);
});

test('can update an existing user', function () {
    $user = User::factory()->create([
        'username' => 'olduser',
        'surname' => 'OldSurname',
    ]);

    Livewire::test(ManageUsers::class)
        ->call('edit', $user->id)
        ->set('username', 'newuser')
        ->set('surname', 'NewSurname')
        ->set('forenames', 'Updated')
        ->set('email', 'updated@example.com')
        ->call('save');

    $user->refresh();
    expect($user->username)->toBe('newuser');
    expect($user->surname)->toBe('NewSurname');
    expect($user->forenames)->toBe('Updated');
    expect($user->email)->toBe('updated@example.com');
});

test('password is optional when updating', function () {
    $user = User::factory()->create(['username' => 'testuser']);
    $originalPassword = $user->password;

    Livewire::test(ManageUsers::class)
        ->call('edit', $user->id)
        ->set('username', 'testuser')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->set('password', '')
        ->call('save');

    $user->refresh();
    expect($user->password)->toBe($originalPassword);
});

test('password is updated when provided during edit', function () {
    $user = User::factory()->create(['username' => 'testuser']);
    $originalPassword = $user->password;

    Livewire::test(ManageUsers::class)
        ->call('edit', $user->id)
        ->set('username', 'testuser')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->set('password', 'newpassword123')
        ->call('save');

    $user->refresh();
    expect($user->password)->not->toBe($originalPassword);
});

test('can delete a user', function () {
    $user = User::factory()->create(['username' => 'testuser']);

    Livewire::test(ManageUsers::class)
        ->call('delete', $user->id);

    expect(User::where('id', $user->id)->exists())->toBeFalse();
});

test('can toggle admin status', function () {
    $user = User::factory()->create(['is_admin' => false]);

    Livewire::test(ManageUsers::class)
        ->call('toggleAdmin', $user->id);

    $user->refresh();
    expect($user->is_admin)->toBeTrue();

    Livewire::test(ManageUsers::class)
        ->call('toggleAdmin', $user->id);

    $user->refresh();
    expect($user->is_admin)->toBeFalse();
});

test('updatedFilter method is called when filter changes', function () {
    User::factory()->count(25)->create(['surname' => 'Alpha', 'username' => 'alpha']);
    User::factory()->count(5)->create(['surname' => 'Beta', 'username' => 'beta']);

    // This tests that the updatedFilter lifecycle hook exists and works
    // which calls resetPage() internally
    Livewire::test(ManageUsers::class)
        ->set('filter', 'alpha')
        ->assertSee('alpha')
        ->set('filter', 'beta')
        ->assertSee('beta')
        ->assertDontSee('alpha');
});

test('can access manage users page via route', function () {
    $response = $this->get(route('admin.users'));

    $response->assertStatus(200)
        ->assertSeeLivewire(ManageUsers::class);
});

test('non-admin cannot create user', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    Livewire::test(ManageUsers::class)
        ->call('create')
        ->set('username', 'newuser')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'newuser@example.com')
        ->set('password', 'password123')
        ->call('save');

    expect(User::where('username', 'newuser')->exists())->toBeFalse();
});

test('non-admin cannot edit user', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    $user = User::factory()->create(['username' => 'original']);

    Livewire::test(ManageUsers::class)
        ->call('edit', $user->id)
        ->set('username', 'updated')
        ->set('surname', 'Smith')
        ->set('forenames', 'John')
        ->set('email', 'test@example.com')
        ->call('save');

    $user->refresh();
    expect($user->username)->toBe('original');
});

test('non-admin cannot delete user', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    $user = User::factory()->create(['username' => 'testuser']);

    Livewire::test(ManageUsers::class)
        ->call('delete', $user->id);

    expect(User::where('id', $user->id)->exists())->toBeTrue();
});

test('non-admin cannot toggle admin status', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    $user = User::factory()->create(['is_admin' => false]);

    Livewire::test(ManageUsers::class)
        ->call('toggleAdmin', $user->id);

    $user->refresh();
    expect($user->is_admin)->toBeFalse();
});

test('non-admin does not see add user button', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    Livewire::test(ManageUsers::class)
        ->assertDontSee('Add User');
});

test('non-admin does not see edit and delete buttons', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    User::factory()->create(['username' => 'testuser']);

    Livewire::test(ManageUsers::class)
        ->assertDontSee('Edit')
        ->assertDontSee('Delete');
});

test('non-admin sees admin badges but they are not clickable', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    $adminUser = User::factory()->create(['is_admin' => true, 'username' => 'admin']);

    Livewire::test(ManageUsers::class)
        ->assertSee('Yes')
        ->assertDontSeeHtml('as="button"');
});

test('admin can see add user button', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->assertSee('Add User');
});

test('admin can see edit and delete buttons', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    User::factory()->create(['username' => 'testuser']);

    Livewire::test(ManageUsers::class)
        ->assertSee('Edit')
        ->assertSee('Delete');
});

test('admin can toggle admin badge by clicking it', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $user = User::factory()->create(['is_admin' => false, 'username' => 'testuser']);

    // Initial state should be non-admin
    expect($user->is_admin)->toBeFalse();

    // Admin should be able to click and toggle
    Livewire::test(ManageUsers::class)
        ->call('toggleAdmin', $user->id);

    $user->refresh();
    expect($user->is_admin)->toBeTrue();
});

test('admin cannot toggle their own admin status', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('toggleAdmin', $admin->id);

    $admin->refresh();
    expect($admin->is_admin)->toBeTrue();
});

test('admin cannot change their own admin status via edit modal', function () {
    $admin = User::factory()->create(['is_admin' => true, 'username' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('edit', $admin->id)
        ->set('username', 'admin')
        ->set('surname', 'Admin')
        ->set('forenames', 'Super')
        ->set('email', $admin->email)
        ->set('isAdmin', false)
        ->call('save');

    $admin->refresh();
    expect($admin->is_admin)->toBeTrue();
});

test('admin does not see admin checkbox when editing themselves', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->call('edit', $admin->id)
        ->assertDontSee('Administrator');
});
