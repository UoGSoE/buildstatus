<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class TagManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_tag_manager_page()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag 1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag 2']);

        $this->actingAs($user)
            ->get(route('admin.tags'))
            ->assertStatus(200)
            ->assertSee('Tag Manager')
            ->assertSee('Tag 1')
            ->assertSee('Tag 2');
    }

    /** @test */
    public function we_can_delete_an_existing_tag()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag 1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag 2']);

        Livewire::actingAs($user)->test('tag-manager')
            ->assertSee('Tag 1')
            ->assertSee('Tag 2')
            ->call('deleteTag', $tag1->id)
            ->assertDontSee('Tag 1')
            ->assertSee('Tag 2');

        $this->assertDatabaseMissing('tags', ['name' => 'Tag 1']);
        $this->assertDatabaseHas('tags', ['name' => 'Tag 2']);
    }

    /** @test */
    public function we_can_create_a_new_tag()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag 1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag 2']);

        Livewire::actingAs($user)->test('tag-manager')
            ->assertSee('Tag 1')
            ->assertSee('Tag 2')
            ->set('newTagName', 'Tag 3')
            ->call('createTag')
            ->assertSee('Tag 1')
            ->assertSee('Tag 2')
            ->assertSee('Tag 3');

        $this->assertDatabaseHas('tags', ['name' => 'Tag 1']);
        $this->assertDatabaseHas('tags', ['name' => 'Tag 2']);
        $this->assertDatabaseHas('tags', ['name' => 'Tag 3']);
    }

    /** @test */
    public function we_cant_create_a_new_tag_with_an_existing_name()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag 1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag 2']);

        Livewire::actingAs($user)->test('tag-manager')
            ->assertSee('Tag 1')
            ->assertSee('Tag 2')
            ->set('newTagName', 'Tag 2')
            ->call('createTag')
            ->assertHasErrors('newTagName')
            ->assertSee('Tag 1')
            ->assertSee('Tag 2');

        $this->assertDatabaseCount('tags', 2);
        $this->assertDatabaseHas('tags', ['name' => 'Tag 1']);
        $this->assertDatabaseHas('tags', ['name' => 'Tag 2']);
    }
}
