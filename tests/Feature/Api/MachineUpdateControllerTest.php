<?php

use App\Jobs\MachineUpdate;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

test('requires authentication', function () {
    $response = $this->postJson('/api/machine', [
        'name' => 'test-machine',
    ]);

    $response->assertUnauthorized();
});

test('requires name field', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    Queue::assertNotPushed(MachineUpdate::class);
});

test('validates name is string and max 255 characters', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', [
        'name' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    Queue::assertNotPushed(MachineUpdate::class);
});

test('validates ip_address is optional but max 255 characters when provided', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', [
        'name' => 'test-machine',
        'ip_address' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('ip_address');

    Queue::assertNotPushed(MachineUpdate::class);
});

test('validates status is optional but max 255 characters when provided', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', [
        'name' => 'test-machine',
        'status' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('status');

    Queue::assertNotPushed(MachineUpdate::class);
});

test('validates lab_name is optional but max 255 characters when provided', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', [
        'name' => 'test-machine',
        'lab_name' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('lab_name');

    Queue::assertNotPushed(MachineUpdate::class);
});

test('dispatches machine update job with valid data', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', [
        'name' => 'test-machine.example.com',
        'ip_address' => '192.168.1.100',
        'status' => 'building',
        'notes' => 'Test notes',
        'lab_name' => 'Lab A',
    ]);

    $response->assertOk()
        ->assertJson(['message' => 'Machine updated']);

    Queue::assertPushed(MachineUpdate::class, function ($job) {
        return $job->data['name'] === 'test-machine.example.com'
            && $job->data['ip_address'] === '192.168.1.100'
            && $job->data['status'] === 'building'
            && $job->data['notes'] === 'Test notes'
            && $job->data['lab_name'] === 'Lab A';
    });
});

test('accepts minimal data with only required name field', function () {
    Queue::fake();
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->postJson('/api/machine', [
        'name' => 'minimal-machine',
    ]);

    $response->assertOk();

    Queue::assertPushed(MachineUpdate::class, function ($job) {
        return $job->data['name'] === 'minimal-machine'
            && !isset($job->data['ip_address'])
            && !isset($job->data['status'])
            && !isset($job->data['notes'])
            && !isset($job->data['lab_name']);
    });
});
