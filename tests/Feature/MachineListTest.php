<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_see_the_machine_list_livewire_component()
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeLivewire('machine-list');
    }

    /** @test */
    public function the_component_displays_the_list_of_all_machines_by_default()
    {
        $this->fail('TODO');
    }

    /** @test */
    public function we_can_search_for_particular_machines()
    {
        $this->fail('TODO');
    }

    /** @test */
    public function we_can_use_tags_to_filter_the_list_of_machines()
    {
        $this->fail('TODO');
    }

    /** @test */
    public function we_can_delete_all_machines_matching_the_current_search_and_tags()
    {
        $this->fail('TODO');
    }
}
