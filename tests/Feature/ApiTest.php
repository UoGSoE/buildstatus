<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Machine;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_send_the_details_of_a_new_machine_via_an_api_call()
    {
        $this->assertEquals(0, Machine::count());

        $response = $this->postJson(route('api.machine.store'), [
            'name' => 'Test Machine',
            'status' => 'Hello World',
            'ip_address' => '192.168.0.54',
            'started_at' => '2021-01-01 00:00:00',
            'finished_at' => '2021-01-01 00:00:00',
            'tags' => ['tag1', 'tag2'],
        ]);

        $response->assertOk();
        $this->assertEquals(1, Machine::count());
        tap(Machine::first(), function ($machine) {
            $this->assertEquals('Test Machine', $machine->name);
            $this->assertEquals('Hello World', $machine->status);
            $this->assertEquals('192.168.0.54', $machine->ip_address);
            $this->assertEquals('2021-01-01 00:00:00', $machine->started_at->format('Y-m-d H:i:s'));
            $this->assertEquals('2021-01-01 00:00:00', $machine->finished_at->format('Y-m-d H:i:s'));
            $this->assertEquals(['tag1', 'tag2'], $machine->tags->pluck('name')->toArray());
        });
    }

    /** @test */
    public function we_can_update_the_details_of_an_existing_machine_via_an_api_call()
    {
        $machine1 = Machine::factory()->create([
            'name' => 'Test Machine 1',
            'status' => 'Hello World',
            'ip_address' => fake()->ipv4(),
            'started_at' => '2021-01-01 01:00:00',
            'finished_at' => '2021-01-01 03:00:00',
        ]);
        $machine2 = Machine::factory()->create([
            'name' => 'Test Machine 2',
            'status' => 'Hello World',
            'ip_address' => fake()->ipv4(),
            'started_at' => '2021-01-02 05:00:00',
            'finished_at' => '2021-01-02 07:00:00',
        ]);
        $machine2->tags()->create(['name' => 'tag1']);
        $machine2->tags()->create(['name' => 'tag2']);

        $this->assertEquals(2, Machine::count());

        $response = $this->postJson(route('api.machine.store'), [
            'name' => 'Test Machine 2',
            'status' => 'Hello Universe',
            'ip_address' => '192.168.0.51',
            'started_at' => '2021-01-04 00:00:00',
            'finished_at' => '2021-01-05 00:00:00',
            'tags' => ['tag1', 'tag3', 'tag4'],
        ]);

        $response->assertOk();
        $this->assertEquals(2, Machine::count());
        tap($machine2->fresh(), function ($machine) {
            $this->assertEquals('Test Machine 2', $machine->name);
            $this->assertEquals('Hello Universe', $machine->status);
            $this->assertEquals('192.168.0.51', $machine->ip_address);
            $this->assertEquals('2021-01-04 00:00:00', $machine->started_at->format('Y-m-d H:i:s'));
            $this->assertEquals('2021-01-05 00:00:00', $machine->finished_at->format('Y-m-d H:i:s'));
            $this->assertEquals(['tag1', 'tag3', 'tag4'], $machine->tags->pluck('name')->toArray());
        });
    }

}
