<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function testUserCanGetTheirProfile()
    {
        $user = User::factory()->create();
 
        $response = $this->actingAs($user)->getJson('/api/v1/profile');
 
        $response->assertStatus(200)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => $user->name]);
    }
 
    public function testUserCanUpdateNameAndEmail()
    {
        $user = User::factory()->create();
 
        $response = $this->actingAs($user)->putJson('/api/v1/profile', [
            'name'  => 'Phoe Updated',
            'email' => 'phoe_updated@example.com',
        ]);
 
        $response->assertStatus(202)
            ->assertJsonStructure(['name', 'email'])
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Phoe Updated']);
 
        $this->assertDatabaseHas('users', [
            'name'  => 'Phoe Updated',
            'email' => 'phoe_updated@example.com',
        ]);
    }
 
    public function testUserCanChangePassword()
    {
        $user = User::factory()->create();
 
        $response = $this->actingAs($user)->putJson('/api/v1/password', [
            'current_password'      => 'password',
            'password'              => 'phoelone3',
            'password_confirmation' => 'phoelone3',
        ]);
 
        $response->assertStatus(202);
    }

    public function testUserCannotUpdatePasswordWithNonMatchingPassword(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/password', [
            'current_password' => 'password',
            'password' => 'phoelone3',
            'password_confirmation' => 'nykglarkwl1',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The password field confirmation does not match.',
            ]);
    }

    public function testUnauthenticatedUserCannotAccessProfile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
