<?php

namespace Tests\Feature;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingSpotApiTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): string
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $response->json('data.token');
    }

    public function test_can_get_available_spots(): void
    {
        // Arrange
        $token = $this->authenticate();
        $parkingLot = ParkingLot::factory()->create();
        ParkingSpot::factory()->count(5)->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
            'is_active' => true,
        ]);
        ParkingSpot::factory()->count(2)->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => true,
            'is_active' => true,
        ]);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/parking-spots/available?parking_lot_id={$parkingLot->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'spot_number', 'is_occupied', 'is_available'],
                ],
            ]);
    }

    public function test_requires_parking_lot_id_for_available_spots(): void
    {
        // Arrange
        $token = $this->authenticate();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/parking-spots/available');

        // Assert
        $response->assertStatus(422);
    }
}




