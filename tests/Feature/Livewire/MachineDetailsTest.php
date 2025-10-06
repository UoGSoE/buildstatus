<?php

use App\Livewire\MachineDetails;
use App\Models\Lab;
use App\Models\Machine;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('component can render', function () {
    $machine = Machine::factory()->create();

    Livewire::test(MachineDetails::class, ['machine' => $machine])
        ->assertStatus(200);
});

test('displays machine details', function () {
    $lab = Lab::factory()->create(['name' => 'Test Lab']);
    $machine = Machine::factory()->create([
        'name' => 'test-machine.example.com',
        'ip_address' => '192.168.1.100',
        'status' => 'building',
        'notes' => 'Test machine notes',
        'lab_id' => $lab->id,
    ]);

    Livewire::test(MachineDetails::class, ['machine' => $machine])
        ->assertSee('test-machine.example.com')
        ->assertSee('192.168.1.100')
        ->assertSee('building')
        ->assertSee('Test machine notes')
        ->assertSee('Test Lab');
});

test('displays logs in descending order', function () {
    $machine = Machine::factory()->create();

    $firstLog = $machine->logs()->create([
        'message' => 'First log',
        'format' => 'text',
    ]);
    $firstLog->created_at = now()->subHours(2);
    $firstLog->save();

    $secondLog = $machine->logs()->create([
        'message' => 'Second log',
        'format' => 'text',
    ]);
    $secondLog->created_at = now()->subHour();
    $secondLog->save();

    $thirdLog = $machine->logs()->create([
        'message' => 'Third log',
        'format' => 'text',
    ]);

    $component = Livewire::test(MachineDetails::class, ['machine' => $machine]);

    $logs = $component->viewData('logs');
    expect($logs->first()->message)->toBe('Third log');
});

test('shows pagination when machine has more than 50 logs', function () {
    $machine = Machine::factory()->create();
    $machine->logs()->createMany(
        collect(range(1, 60))->map(fn ($i) => [
            'message' => "Log entry {$i}",
            'format' => 'text',
        ])->toArray()
    );

    Livewire::test(MachineDetails::class, ['machine' => $machine])
        ->assertSee('Next');
});

test('displays no logs message when machine has no logs', function () {
    $machine = Machine::factory()->create();

    Livewire::test(MachineDetails::class, ['machine' => $machine])
        ->assertSee('No logs found');
});

test('displays logs when they exist', function () {
    $machine = Machine::factory()->create();
    $machine->logs()->create([
        'message' => 'Test log message',
        'format' => 'text',
    ]);

    Livewire::test(MachineDetails::class, ['machine' => $machine])
        ->assertSee('Test log message');
});

test('can access machine details page via route', function () {
    $machine = Machine::factory()->create(['name' => 'route-test-machine']);

    $response = $this->get(route('machine.details', $machine));

    $response->assertStatus(200)
        ->assertSeeLivewire(MachineDetails::class)
        ->assertSee('route-test-machine');
});
