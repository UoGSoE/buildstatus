<?php

use App\Livewire\MachineList;
use App\Models\Lab;
use App\Models\Machine;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('component can render', function () {
    Livewire::test(MachineList::class)
        ->assertStatus(200);
});

test('displays machines', function () {
    $lab = Lab::factory()->create(['name' => 'Lab A']);
    Machine::factory()->create([
        'name' => 'machine1.example.com',
        'status' => 'building',
        'lab_id' => $lab->id,
    ]);

    Livewire::test(MachineList::class)
        ->assertSee('machine1')
        ->assertSee('Lab A')
        ->assertSee('Building');
});

test('filters machines by search term in name', function () {
    Machine::factory()->create(['name' => 'alpha-machine']);
    Machine::factory()->create(['name' => 'beta-machine']);

    Livewire::test(MachineList::class)
        ->set('filter', 'alpha')
        ->assertSee('alpha-machine')
        ->assertDontSee('beta-machine');
});

test('filters machines by search term in ip address', function () {
    Machine::factory()->create([
        'name' => 'machine1',
        'ip_address' => '192.168.1.100',
    ]);
    Machine::factory()->create([
        'name' => 'machine2',
        'ip_address' => '10.0.0.50',
    ]);

    Livewire::test(MachineList::class)
        ->set('filter', '192.168')
        ->assertSee('machine1')
        ->assertDontSee('machine2');
});

test('filters machines by search term in status', function () {
    Machine::factory()->create([
        'name' => 'machine1',
        'status' => 'building',
    ]);
    Machine::factory()->create([
        'name' => 'machine2',
        'status' => 'idle',
    ]);

    Livewire::test(MachineList::class)
        ->set('filter', 'build')
        ->assertSee('machine1')
        ->assertDontSee('machine2');
});

test('filters machines by lab', function () {
    $labA = Lab::factory()->create(['name' => 'Lab A']);
    $labB = Lab::factory()->create(['name' => 'Lab B']);

    Machine::factory()->create([
        'name' => 'machine-a',
        'lab_id' => $labA->id,
    ]);
    Machine::factory()->create([
        'name' => 'machine-b',
        'lab_id' => $labB->id,
    ]);

    Livewire::test(MachineList::class)
        ->set('labId', $labA->id)
        ->assertSee('machine-a')
        ->assertDontSee('machine-b');
});

test('shows all machines when no lab filter is applied', function () {
    $lab = Lab::factory()->create();
    Machine::factory()->create([
        'name' => 'machine-a',
        'lab_id' => $lab->id,
    ]);
    Machine::factory()->create(['name' => 'machine-b']);

    Livewire::test(MachineList::class)
        ->set('labId', '')
        ->assertSee('machine-a')
        ->assertSee('machine-b');
});

test('displays machines with pagination', function () {
    Machine::factory()->count(105)->create();

    Livewire::test(MachineList::class)
        ->assertSee('Next');
});

test('auto refresh is enabled by default', function () {
    Livewire::test(MachineList::class)
        ->assertSet('autoRefresh', true);
});

test('can toggle auto refresh', function () {
    Livewire::test(MachineList::class)
        ->assertSet('autoRefresh', true)
        ->set('autoRefresh', false)
        ->assertSet('autoRefresh', false);
});

test('only applies filter when more than 1 character', function () {
    Machine::factory()->create(['name' => 'alpha-machine']);
    Machine::factory()->create(['name' => 'beta-machine']);

    Livewire::test(MachineList::class)
        ->set('filter', 'a')
        ->assertSee('alpha-machine')
        ->assertSee('beta-machine');
});

test('displays lab dropdown with all labs', function () {
    Lab::factory()->create(['name' => 'Lab Alpha']);
    Lab::factory()->create(['name' => 'Lab Beta']);

    Livewire::test(MachineList::class)
        ->assertSee('Lab Alpha')
        ->assertSee('Lab Beta');
});

test('orders machines by updated_at descending', function () {
    $older = Machine::factory()->create([
        'name' => 'older-machine',
        'updated_at' => now()->subHours(2),
    ]);
    $newer = Machine::factory()->create([
        'name' => 'newer-machine',
        'updated_at' => now()->subHours(1),
    ]);

    $component = Livewire::test(MachineList::class);

    $machines = $component->viewData('machines');
    expect($machines->first()->id)->toBe($newer->id);
});

test('can show machine details in modal', function () {
    $machine = Machine::factory()->create(['name' => 'test-machine']);

    Livewire::test(MachineList::class)
        ->call('showMachineDetails', $machine->id)
        ->assertSet('machineDetails.id', $machine->id);
});

test('loads latest 10 logs for machine details', function () {
    $lab = Lab::factory()->create();
    $machine = Machine::factory()->create(['lab_id' => $lab->id]);
    $machine->logs()->createMany(
        collect(range(1, 15))->map(fn ($i) => [
            'message' => "Log entry {$i}",
            'format' => 'text',
        ])->toArray()
    );

    $component = Livewire::test(MachineList::class);
    $machines = $component->viewData('machines');

    $firstMachine = $machines->first();
    expect($firstMachine->logs)->toHaveCount(10);
});
