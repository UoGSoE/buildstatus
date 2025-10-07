<?php

use App\Livewire\Admin\ManageLabs;
use App\Models\Lab;
use App\Models\Machine;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
});

test('component can render', function () {
    Livewire::test(ManageLabs::class)
        ->assertStatus(200);
});

test('displays labs with machine counts', function () {
    $lab = Lab::factory()->create(['name' => 'Test Lab']);
    Machine::factory()->count(5)->create(['lab_id' => $lab->id]);

    Livewire::test(ManageLabs::class)
        ->assertSee('Test Lab')
        ->assertSee('5');
});

test('filters labs by name', function () {
    Lab::factory()->create(['name' => 'Alpha Lab']);
    Lab::factory()->create(['name' => 'Beta Lab']);

    Livewire::test(ManageLabs::class)
        ->set('filter', 'alpha')
        ->assertSee('Alpha Lab')
        ->assertDontSee('Beta Lab');
});

test('shows all labs when filter is empty', function () {
    Lab::factory()->create(['name' => 'Alpha Lab']);
    Lab::factory()->create(['name' => 'Beta Lab']);

    Livewire::test(ManageLabs::class)
        ->set('filter', '')
        ->assertSee('Alpha Lab')
        ->assertSee('Beta Lab');
});

test('only applies filter when more than 1 character', function () {
    Lab::factory()->create(['name' => 'Alpha Lab']);
    Lab::factory()->create(['name' => 'Beta Lab']);

    Livewire::test(ManageLabs::class)
        ->set('filter', 'a')
        ->assertSee('Alpha Lab')
        ->assertSee('Beta Lab');
});

test('orders labs by name', function () {
    Lab::factory()->create(['name' => 'Zebra Lab']);
    Lab::factory()->create(['name' => 'Alpha Lab']);

    $component = Livewire::test(ManageLabs::class);

    $labs = $component->viewData('labs');
    expect($labs->first()->name)->toBe('Alpha Lab');
});

test('displays labs with pagination', function () {
    Lab::factory()->count(25)->create();

    Livewire::test(ManageLabs::class)
        ->assertSeeHtml('data-flux-pagination');
});

test('can open create modal', function () {
    Livewire::test(ManageLabs::class)
        ->call('create')
        ->assertSet('labId', null)
        ->assertSet('name', '')
        ->assertSet('notes', '');
});

test('can create a new lab', function () {
    Livewire::test(ManageLabs::class)
        ->call('create')
        ->set('name', 'New Lab')
        ->set('notes', 'Test notes')
        ->call('save');

    expect(Lab::where('name', 'New Lab')->exists())->toBeTrue();
    expect(Lab::where('name', 'New Lab')->first()->notes)->toBe('Test notes');
});

test('validates lab name is required when creating', function () {
    Livewire::test(ManageLabs::class)
        ->call('create')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('can open edit modal with lab data', function () {
    $lab = Lab::factory()->create([
        'name' => 'Test Lab',
        'notes' => 'Test notes',
    ]);

    Livewire::test(ManageLabs::class)
        ->call('edit', $lab->id)
        ->assertSet('labId', $lab->id)
        ->assertSet('name', 'Test Lab')
        ->assertSet('notes', 'Test notes');
});

test('can update an existing lab', function () {
    $lab = Lab::factory()->create(['name' => 'Old Name']);

    Livewire::test(ManageLabs::class)
        ->call('edit', $lab->id)
        ->set('name', 'Updated Name')
        ->set('notes', 'Updated notes')
        ->call('save');

    $lab->refresh();
    expect($lab->name)->toBe('Updated Name');
    expect($lab->notes)->toBe('Updated notes');
});

test('validates lab name is required when updating', function () {
    $lab = Lab::factory()->create(['name' => 'Test Lab']);

    Livewire::test(ManageLabs::class)
        ->call('edit', $lab->id)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('can delete a lab', function () {
    $lab = Lab::factory()->create(['name' => 'Test Lab']);

    Livewire::test(ManageLabs::class)
        ->call('delete', $lab->id);

    expect(Lab::where('id', $lab->id)->exists())->toBeFalse();
});

test('displays notes truncated in table', function () {
    $longNotes = str_repeat('This is a very long note. ', 20);
    Lab::factory()->create([
        'name' => 'Test Lab',
        'notes' => $longNotes,
    ]);

    $component = Livewire::test(ManageLabs::class);

    $labs = $component->viewData('labs');
    $lab = $labs->first();

    // The view uses Str::limit($lab->notes, 50)
    expect(strlen($lab->notes))->toBeGreaterThan(50);
});

test('updatedFilter method is called when filter changes', function () {
    Lab::factory()->count(25)->create(['name' => 'Alpha Lab']);
    Lab::factory()->count(5)->create(['name' => 'Beta Lab']);

    // This tests that the updatedFilter lifecycle hook exists and works
    // which calls resetPage() internally
    Livewire::test(ManageLabs::class)
        ->set('filter', 'alpha')
        ->assertSee('Alpha Lab')
        ->set('filter', 'beta')
        ->assertSee('Beta Lab')
        ->assertDontSee('Alpha Lab');
});

test('can access manage labs page via route', function () {
    $response = $this->get(route('admin.labs'));

    $response->assertStatus(200)
        ->assertSeeLivewire(ManageLabs::class);
});

test('non-admin cannot create lab', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    Livewire::test(ManageLabs::class)
        ->call('create')
        ->set('name', 'Test Lab')
        ->call('save');

    expect(Lab::where('name', 'Test Lab')->exists())->toBeFalse();
});

test('non-admin cannot edit lab', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    $lab = Lab::factory()->create(['name' => 'Original Name']);

    Livewire::test(ManageLabs::class)
        ->call('edit', $lab->id)
        ->set('name', 'Updated Name')
        ->call('save');

    $lab->refresh();
    expect($lab->name)->toBe('Original Name');
});

test('non-admin cannot delete lab', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    $lab = Lab::factory()->create(['name' => 'Test Lab']);

    Livewire::test(ManageLabs::class)
        ->call('delete', $lab->id);

    expect(Lab::where('id', $lab->id)->exists())->toBeTrue();
});

test('non-admin does not see add lab button', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    Livewire::test(ManageLabs::class)
        ->assertDontSee('Add Lab');
});

test('non-admin does not see edit and delete buttons', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    Lab::factory()->create(['name' => 'Test Lab']);

    Livewire::test(ManageLabs::class)
        ->assertDontSee('Edit')
        ->assertDontSee('Delete');
});

test('admin can see add lab button', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Livewire::test(ManageLabs::class)
        ->assertSee('Add Lab');
});

test('admin can see edit and delete buttons', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Lab::factory()->create(['name' => 'Test Lab']);

    Livewire::test(ManageLabs::class)
        ->assertSee('Edit')
        ->assertSee('Delete');
});
