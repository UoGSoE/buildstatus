<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Ohffs\Ldap\FakeLdapConnection;
use Ohffs\Ldap\LdapConnectionInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_logged_in_users_are_redirected_to_the_login_page()
    {
        $this->get(route('home'))
            ->assertRedirect(route('login'));
        $this->get(route('admin.access'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function only_existing_users_can_login()
    {
        $user = User::factory()->create(['username' => 'test-user']);

        $response = $this->from(route('login'))->post(route('login'), [
            'username' => 'not-a-user',
            'password' => 'not-a-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function existing_users_can_log_in()
    {
        $this->fakeLdapConnection();
        $user = User::factory()->create(['username' => 'validuser']);

        $response = $this->from(route('login'))->post(route('login'), [
            'username' => 'validuser',
            'password' => 'whatever',
        ]);

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function logged_in_users_can_log_out()
    {
        $user = User::factory()->create(['username' => 'test-user']);
        auth()->login($user);

        $this->assertTrue(auth()->check());

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertFalse(auth()->check());
    }

    /** @test */
    public function logged_in_users_who_visit_the_login_page_are_redirected_home()
    {
        $user = User::factory()->create(['username' => 'test-user']);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('home'));
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
