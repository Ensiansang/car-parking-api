<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ZoneTest extends TestCase
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

    public function testPublicUserCanGetAllZones()
    {
        $response = $this->getJson('/api/v1/zones');
 
        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [
                ['*' => 'id', 'name', 'price_per_hour'],
            ]])
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonPath('data.0.name', 'Green Zone')
            ->assertJsonPath('data.0.price_per_hour', 500)
            ->assertJsonPath('data.1.id', 2)
            ->assertJsonPath('data.1.name', 'Yellow Zone')
            ->assertJsonPath('data.1.price_per_hour', 700)
            ->assertJsonPath('data.2.id', 3)
            ->assertJsonPath('data.2.name', 'Red Zone')
            ->assertJsonPath('data.2.price_per_hour', 900);
    }

    public function testPublicUserCanGetSingleZone()
    {
        $response = $this->getJson('/api/v1/zones/1');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1)
            ->assertJsonPath('data.id', 1)
            ->assertJsonPath('data.name', 'Green Zone')
            ->assertJsonPath('data.price_per_hour', 500);
    }
}
