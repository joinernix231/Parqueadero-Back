<?php

namespace Tests\Feature;

use App\Models\ParkingLot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingLotApiTest extends TestCase
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

    public function test_can_list_parking_lots(): void
    {
        // Arrange
        $token = $this->authenticate();
        ParkingLot::factory()->count(3)->create();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/parking-lots');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'address', 'total_spots', 'hourly_rate_day', 'hourly_rate_night'],
                ],
            ]);
    }

    public function test_can_get_parking_lot_by_id(): void
    {
        // Arrange
        $token = $this->authenticate();
        $lot = ParkingLot::factory()->create();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/parking-lots/{$lot->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $lot->id,
                    'name' => $lot->name,
                ],
            ]);
    }

    public function test_returns_404_when_parking_lot_not_found(): void
    {
        // Arrange
        $token = $this->authenticate();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/parking-lots/99999');

        // Assert
        $response->assertStatus(404);
    }
}
