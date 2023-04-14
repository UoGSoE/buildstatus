<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MachineListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_see_the_machine_list_livewire_component(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSeeLivewire('machine-list');
    }

    /** @test */
    public function the_component_displays_the_list_of_all_machines_by_default(): void
    {
        $machine1 = Machine::factory()->create(['name' => 'Machine 1']);
        $machine2 = Machine::factory()->create(['name' => 'Machine 2']);
        $machine3 = Machine::factory()->create(['name' => 'Machine 3']);

        Livewire::test('machine-list')
            ->assertSee($machine1->name)
            ->assertSee($machine2->name)
            ->assertSee($machine3->name);
    }

    /** @test */
    public function we_can_search_for_particular_machines(): void
    {
        $machine1 = Machine::factory()->create(['name' => 'Machine 1']);
        $machine2 = Machine::factory()->create(['name' => 'Machine 2']);
        $machine3 = Machine::factory()->create(['name' => 'Machine 3']);

        Livewire::test('machine-list')
            ->assertSee($machine1->name)
            ->assertSee($machine2->name)
            ->assertSee($machine3->name)
            ->set('search', 'Machine 1')
            ->assertSee($machine1->name)
            ->assertDontSee($machine2->name)
            ->assertDontSee($machine3->name);
    }

    /** @test */
    public function we_can_use_tags_to_filter_the_list_of_machines(): void
    {
        $machine1 = Machine::factory()->create(['name' => 'Machine 1']);
        $machine2 = Machine::factory()->create(['name' => 'Machine 2']);
        $machine3 = Machine::factory()->create(['name' => 'Machine 3']);
        $engineeringTag = Tag::factory()->create(['name' => 'Engineering']);
        $compsciTag = Tag::factory()->create(['name' => 'Computer Science']);
        $laptopTag = Tag::factory()->create(['name' => 'Laptop']);
        $labmachineTag = Tag::factory()->create(['name' => 'Lab Machine']);
        $machine1->tags()->attach($engineeringTag);
        $machine1->tags()->attach($laptopTag);
        $machine2->tags()->attach($engineeringTag);
        $machine2->tags()->attach($labmachineTag);
        $machine3->tags()->attach($compsciTag);
        $machine3->tags()->attach($labmachineTag);

        Livewire::test('machine-list')
            ->assertSee($machine1->name)
            ->assertSee($machine2->name)
            ->assertSee($machine3->name)
            ->set('tags', [$engineeringTag->id])
            ->assertSee($machine1->name)
            ->assertSee($machine2->name)
            ->assertDontSee($machine3->name)
            ->set('tags', [$engineeringTag->id, $laptopTag->id])
            ->assertSee($machine1->name)
            ->assertDontSee($machine2->name)
            ->assertDontSee($machine3->name);
    }

    /** @test */
    public function we_can_delete_all_machines_matching_the_current_search_and_tags(): void
    {
        config(['buildstatus.admin_password' => 'secret']);
        $machine1 = Machine::factory()->create(['name' => 'Machine 1']);
        $machine2 = Machine::factory()->create(['name' => 'Machine 2']);
        $machine3 = Machine::factory()->create(['name' => 'Machine 3']);
        $engineeringTag = Tag::factory()->create(['name' => 'Engineering']);
        $compsciTag = Tag::factory()->create(['name' => 'Computer Science']);
        $laptopTag = Tag::factory()->create(['name' => 'Laptop']);
        $labmachineTag = Tag::factory()->create(['name' => 'Lab Machine']);
        $machine1->tags()->attach($engineeringTag);
        $machine1->tags()->attach($laptopTag);
        $machine2->tags()->attach($engineeringTag);
        $machine2->tags()->attach($labmachineTag);
        $machine3->tags()->attach($compsciTag);
        $machine3->tags()->attach($labmachineTag);

        Livewire::test('machine-list')
            ->assertSee($machine1->name)
            ->assertSee($machine2->name)
            ->assertSee($machine3->name)
            ->set('tags', [$engineeringTag->id])
            ->assertSee($machine1->name)
            ->assertSee($machine2->name)
            ->assertDontSee($machine3->name)
            ->set('password', 'secret')
            ->call('truncateMachines')
            ->set('tags', [])
            ->assertDontSee($machine1->name)
            ->assertDontSee($machine2->name)
            ->assertSee($machine3->name);

        $this->assertDatabaseMissing('machines', ['name' => $machine1->name]);
        $this->assertDatabaseMissing('machines', ['name' => $machine2->name]);
        $this->assertDatabaseHas('machines', ['name' => $machine3->name]);
    }
}
