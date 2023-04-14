<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Ohffs\Ldap\FakeLdapConnection;
use Ohffs\Ldap\LdapConnectionInterface;
use Ohffs\Ldap\LdapUser;

class AccessManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_list_of_current_users_in_the_list()
    {
        $user1 = User::factory()->create(['surname' => 'Smith']);
        $user2 = User::factory()->create(['surname' => 'Jones']);
        $user3 = User::factory()->create(['surname' => 'Brown']);

        Livewire::actingAs($user1)->test('access-manager')
            ->assertSeeHtml('Smith')
            ->assertSeeHtml('Jones')
            ->assertSeeHtml('Brown');
    }

    /** @test */
    public function we_can_delete_a_user_from_the_system()
    {
        $user1 = User::factory()->create(['surname' => 'Smith']);
        $user2 = User::factory()->create(['surname' => 'Jones']);
        $user3 = User::factory()->create(['surname' => 'Brown']);

        Livewire::actingAs($user1)->test('access-manager')
            ->assertSeeHtml('Smith')
            ->assertSeeHtml('Jones')
            ->assertSeeHtml('Brown')
            ->call('deleteUser', $user2->id)
            ->assertSeeHtml('Smith')
            ->assertDontSeeHtml('Jones')
            ->assertSeeHtml('Brown');
    }

    /** @test */
    public function a_user_cant_delete_themselves()
    {
        $user1 = User::factory()->create(['surname' => 'Smith']);
        $user2 = User::factory()->create(['surname' => 'Jones']);
        $user3 = User::factory()->create(['surname' => 'Brown']);

        Livewire::actingAs($user1)->test('access-manager')
            ->assertSeeHtml('Smith')
            ->assertSeeHtml('Jones')
            ->assertSeeHtml('Brown')
            ->call('deleteUser', $user1->id)
            ->assertSeeHtml('Smith')
            ->assertSeeHtml('Jones')
            ->assertSeeHtml('Brown');

        $this->assertDatabaseHas('users', ['id' => $user1->id]);
    }

    /** @test */
    public function we_can_toggle_showing_the_form_to_create_a_new_user()
    {
        $user1 = User::factory()->create(['surname' => 'Smith']);
        $user2 = User::factory()->create(['surname' => 'Jones']);
        $user3 = User::factory()->create(['surname' => 'Brown']);

        Livewire::actingAs($user1)->test('access-manager')
            ->assertSeeHtml('Smith')
            ->assertSeeHtml('Jones')
            ->assertSeeHtml('Brown')
            ->assertDontSee('Create new user')
            ->set('showCreateForm', true)
            ->assertSeeHtml('Create new user')
            ->assertSeeHtml('Username')
            ->assertSeeHtml('Surname')
            ->set('showCreateForm', false)
            ->assertDontSeeHtml('Create new user');
    }

    /** @test */
    public function we_can_create_a_new_user_via_an_ldap_lookup()
    {
        $this->fakeLdapConnection();
        $user1 = User::factory()->create(['surname' => 'Smith']);
        $user2 = User::factory()->create(['surname' => 'Jones']);
        $user3 = User::factory()->create(['surname' => 'Brown']);

        Livewire::actingAs($user1)->test('access-manager')
            ->assertSeeHtml('Smith')
            ->assertSeeHtml('Jones')
            ->assertSeeHtml('Brown')
            ->set('showCreateForm', true)
            ->set('username', 'validuser')
            ->call('lookupUser')
            ->assertSet('username', 'validuser')
            ->assertSet('email', 'validuser@example.com')
            ->assertSet('forenames', 'forenames')
            ->assertSet('surname', 'surname')
            ->set('description', 'la la la')
            ->call('createUser')
            ->assertSet('username', '');

        $this->assertDatabaseHas('users', [
            'username' => 'validuser',
            'email' => 'validuser@example.com',
            'forenames' => 'forenames',
            'surname' => 'surname',
            'description' => 'la la la'
        ]);
    }

    private function fakeLdapConnection()
    {
        // the FakeLdapConnection returns a 'validuser' user
        $this->instance(
            LdapConnectionInterface::class,
            new FakeLdapConnection('up', 'whatever')
        );
    }
}
