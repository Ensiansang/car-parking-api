<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
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

    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = User::factory()->create();
 
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);
 
        $response->assertStatus(201);
    }
 
    public function testUserCannotLoginWithIncorrectCredentials()
    {
        $user = User::factory()->create();
 
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong_password',
        ]);
 
        $response->assertStatus(422);
    }

    public function testUserCanRegisterWithCorrectCredentials()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Phoe Lone',
            'email'                 => 'phonelone@example.com',
            'password'              => '12345678',
            'password_confirmation' => '12345678',
        ]);
     
        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
            ]);
     
        $this->assertDatabaseHas('users', [
            'name'  => 'Phoe Lone',
            'email' => 'phonelone@example.com',
        ]);
    }
     
    public function testUserCannotRegisterWithIncorrectCredentials()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Phoe Lone',
            'email'                 => 'phonelone@example.com',
            'password'              => '12345678',
            'password_confirmation' => 'phoelone',
        ]);
     
        $response->assertStatus(422);
     
        $this->assertDatabaseMissing('users', [
            'name'  => 'Phoe Lone',
            'email' => 'phonelone@example.com',
        ]);
    }


}
