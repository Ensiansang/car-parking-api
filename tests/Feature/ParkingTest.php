<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Parking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;

class ParkingTest extends TestCase
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

        
    public function testUserCanStartParking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();
 
        $response = $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);
 
        $response->assertStatus(201)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time'  => now()->toDateTimeString(),
                    'stop_time'   => null,
                    'total_price' => 0,
                ],
            ]);
 
        $this->assertDatabaseCount('parkings', '1');
    }
 
    public function testUserCanGetOngoingParkingWithCorrectPrice()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();
 
        $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);
 
        $this->travel(2)->hours();
 
        $parking = Parking::first();
        $response = $this->actingAs($user)->getJson('/api/v1/parkings/' . $parking->id);
 
        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'stop_time'   => null,
                    'total_price' => $zone->price_per_hour * 2 + 1,
                ],
            ]);
    }
 
    public function testUserCanStopParking()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $zone = Zone::first();
 
        $this->actingAs($user)->postJson('/api/v1/parkings/start', [
            'vehicle_id' => $vehicle->id,
            'zone_id'    => $zone->id,
        ]);
 
        $this->travel(2)->hours();
 
        $parking = Parking::first();
        $response = $this->actingAs($user)->putJson('/api/v1/parkings/' . $parking->id);
 
        $updatedParking = Parking::find($parking->id);
 
        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJson([
                'data' => [
                    'start_time'  => $updatedParking->start_time->toDateTimeString(),
                    'stop_time'   => $updatedParking->stop_time->toDateTimeString(),
                    'total_price' => $updatedParking->total_price,
                ],
            ]);
 
        $this->assertDatabaseCount('parkings', '1');
    }

    // public function testUserPassingEmptyParametersCanSeeCorrectValidationMessages(): void
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/v1/parkings/start', [
    //         'vehicle_id' => '',
    //         'zone_id' => '',
    //     ]);

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'The vehicle id field is required. (and 1 more error)',
    //             'errors' => [
    //                 'vehicle_id' => [
    //                     'The vehicle id field is required.',
    //                 ],
    //                 'zone_id' => [
    //                     'The zone id field is required.',
    //                 ],
    //             ],
    //         ]);
    // }

    // public function testUserPassingEmptyVehicleIdParameterCanSeeCorrectValidationMessages(): void
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/v1/parkings/start', [
    //         'vehicle_id' => '',
    //         'zone_id' => $this->zone->id,
    //     ]);

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'The vehicle id field is required.',
    //             'errors' => [
    //                 'vehicle_id' => [
    //                     'The vehicle id field is required.',
    //                 ],
    //             ],
    //         ]);
    // }

    // public function testUserPassingEmptyZoneIdParameterCanSeeCorrectValidationMessages(): void
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/v1/parkings/start', [
    //         'vehicle_id' => $this->vehicle->id,
    //         'zone_id' => '',
    //     ]);

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'The zone id field is required.',
    //             'errors' => [
    //                 'zone_id' => [
    //                     'The zone id field is required.',
    //                 ],
    //             ],
    //         ]);
    // }

    // public function testUserPassingIncorrectParameterTypesCanSeeCorrectValidationMessages(): void
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/v1/parkings/start', [
    //         'vehicle_id' => 'INCORRECT TYPE',
    //         'zone_id' => 'INCORRECT TYPE',
    //     ]);

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'The vehicle id must be an integer. (and 1 more error)',
    //             'errors' => [
    //                 'vehicle_id' => [
    //                     'The vehicle id must be an integer.',
    //                 ],
    //                 'zone_id' => [
    //                     'The zone id must be an integer.',
    //                 ],
    //             ],
    //         ]);
    // }

    // public function testUserPassingIncorrectParameterVehicleTypeCanSeeCorrectValidationMessages(): void
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/v1/parkings/start', [
    //         'vehicle_id' => 'INCORRECT TYPE',
    //         'zone_id' => $this->zone->id,
    //     ]);

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'The vehicle id must be an integer.',
    //             'errors' => [
    //                 'vehicle_id' => [
    //                     'The vehicle id must be an integer.',
    //                 ],
    //             ],
    //         ]);
    // }

    // public function testUserPassingIncorrectParameterZoneTypeCanSeeCorrectValidationMessages(): void
    // {
    //     $response = $this->actingAs($this->user)->postJson('/api/v1/parkings/start', [
    //         'vehicle_id' => $this->vehicle->id,
    //         'zone_id' => 'INCORRECT TYPE',
    //     ]);

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'The zone id must be an integer.',
    //             'errors' => [
    //                 'zone_id' => [
    //                     'The zone id must be an integer.',
    //                 ],
    //             ],
    //         ]);
    // }
}
