<?php

use App\Jobs\MachineUpdate;
use App\Models\Lab;
use App\Models\Machine;

test('creates new machine with all fields', function () {
    $job = new MachineUpdate([
        'name' => 'test-machine.example.com',
        'ip_address' => '192.168.1.100',
        'status' => 'building',
        'notes' => 'Test notes',
        'lab_name' => 'Lab A',
    ]);

    $job->handle();

    expect(Machine::count())->toBe(1);

    $machine = Machine::first();
    expect($machine->name)->toBe('test-machine.example.com')
        ->and($machine->ip_address)->toBe('192.168.1.100')
        ->and($machine->status)->toBe('building')
        ->and($machine->notes)->toBe('Test notes')
        ->and($machine->lab)->not->toBeNull()
        ->and($machine->lab->name)->toBe('Lab A');
});

test('updates existing machine', function () {
    $lab = Lab::factory()->create(['name' => 'Lab B']);
    $machine = Machine::factory()->create([
        'name' => 'existing-machine',
        'ip_address' => '10.0.0.1',
        'status' => 'idle',
        'notes' => 'Original notes',
        'lab_id' => $lab->id,
    ]);

    $job = new MachineUpdate([
        'name' => 'existing-machine',
        'status' => 'building',
        'lab_name' => 'Lab B',
    ]);

    $job->handle();

    expect(Machine::count())->toBe(1);

    $machine->refresh();
    expect($machine->name)->toBe('existing-machine')
        ->and($machine->ip_address)->toBe('10.0.0.1')
        ->and($machine->status)->toBe('building')
        ->and($machine->notes)->toBe('Original notes')
        ->and($machine->lab_id)->toBe($lab->id);
});

test('creates lab if it does not exist', function () {
    $job = new MachineUpdate([
        'name' => 'test-machine',
        'lab_name' => 'New Lab',
    ]);

    $job->handle();

    expect(Lab::count())->toBe(1);
    expect(Lab::first()->name)->toBe('New Lab');
});

test('uses existing lab if it already exists', function () {
    $existingLab = Lab::factory()->create(['name' => 'Existing Lab']);

    $job = new MachineUpdate([
        'name' => 'test-machine',
        'lab_name' => 'Existing Lab',
    ]);

    $job->handle();

    expect(Lab::count())->toBe(1);
    expect(Machine::first()->lab_id)->toBe($existingLab->id);
});

test('handles updates without lab_name by preserving existing lab_id', function () {
    // API validation prevents NEW machines without lab, but Job must handle
    // updates where lab_name is omitted (preserving existing lab)
    $job = new MachineUpdate([
        'name' => 'standalone-machine',
        'status' => 'idle',
    ]);

    $job->handle();

    $machine = Machine::first();
    expect($machine->lab_id)->toBeNull();
});

test('filters out null values from machine fields', function () {
    $lab = Lab::factory()->create(['name' => 'Test Lab']);
    $machine = Machine::factory()->create([
        'name' => 'test-machine',
        'ip_address' => '192.168.1.50',
        'status' => 'idle',
        'notes' => 'Existing notes',
        'lab_id' => $lab->id,
    ]);

    $job = new MachineUpdate([
        'name' => 'test-machine',
        'status' => 'building',
        // ip_address, notes, and lab_name intentionally not provided
    ]);

    $job->handle();

    $machine->refresh();
    expect($machine->name)->toBe('test-machine')
        ->and($machine->ip_address)->toBe('192.168.1.50')
        ->and($machine->status)->toBe('building')
        ->and($machine->notes)->toBe('Existing notes')
        ->and($machine->lab_id)->toBe($lab->id);
});

test('creates log entry after machine update', function () {
    $job = new MachineUpdate([
        'name' => 'test-machine',
        'status' => 'building',
    ]);

    $job->handle();

    $machine = Machine::first();
    expect($machine->logs()->count())->toBe(1);

    $log = $machine->logs->first();
    expect($log->format)->toBe('json');

    $logData = json_decode($log->message, true);
    expect($logData)->toHaveKey('name')
        ->and($logData)->toHaveKey('status')
        ->and($logData['name'])->toBe('test-machine')
        ->and($logData['status'])->toBe('building');
});

test('updates machine and creates new log each time', function () {
    $machine = Machine::factory()->create(['name' => 'test-machine']);

    $job1 = new MachineUpdate([
        'name' => 'test-machine',
        'status' => 'building',
    ]);
    $job1->handle();

    $job2 = new MachineUpdate([
        'name' => 'test-machine',
        'status' => 'ready',
    ]);
    $job2->handle();

    $machine->refresh();
    expect($machine->logs()->count())->toBe(2)
        ->and($machine->status)->toBe('ready');
});
