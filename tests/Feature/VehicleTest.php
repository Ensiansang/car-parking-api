<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;

class VehicleTest extends TestCase
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

    public function testUserCanGetTheirOwnVehicles()
    {
        $phoe = User::factory()->create();
        $vehicleForPhoe = Vehicle::factory()->create([
            'user_id' => $phoe->id
        ]);
 
        $myo = User::factory()->create();
        $vehicleForMyo = Vehicle::factory()->create([
            'user_id' => $myo->id
        ]);
 
        $response = $this->actingAs($phoe)->getJson('/api/v1/vehicles');
 
        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.plate_number', $vehicleForPhoe->plate_number)
            ->assertJsonMissing($vehicleForMyo->toArray());
    }
    public function testUserCanCreateVehicle()
    {
        $user = User::factory()->create();
 
        $response = $this->actingAs($user)->postJson('/api/v1/vehicles', [
            'plate_number' => 'AAA111',
        ]);
 
        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => ['0' => 'plate_number'],
            ])
            ->assertJsonPath('data.plate_number', 'AAA111');
 
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA111',
        ]);
    }
 
    public function testUserCanUpdateTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
 
        $response = $this->actingAs($user)->putJson('/api/v1/vehicles/' . $vehicle->id, [
            'plate_number' => 'AAA123',
        ]);
 
        $response->assertStatus(202)
            ->assertJsonStructure(['plate_number'])
            ->assertJsonPath('plate_number', 'AAA123');
 
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AAA123',
        ]);
    }
 
    public function testUserCanDeleteTheirVehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
 
        $response = $this->actingAs($user)->deleteJson('/api/v1/vehicles/' . $vehicle->id);
 
        $response->assertNoContent();
 
        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
            'deleted_at' => NULL
        ])->assertDatabaseCount('vehicles', 0); // we have SoftDeletes, remember?
    }
}
