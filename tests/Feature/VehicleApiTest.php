<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleApiTest extends TestCase
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

    public function test_can_list_vehicles(): void
    {
        // Arrange
        $token = $this->authenticate();
        Vehicle::factory()->count(5)->create();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/vehicles');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'plate', 'owner_name', 'phone', 'vehicle_type'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_create_vehicle(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicleData = [
            'plate' => 'ABC123',
            'owner_name' => 'Juan Pérez',
            'phone' => '3001234567',
            'vehicle_type' => 'car',
        ];

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vehicles', $vehicleData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'plate', 'owner_name', 'phone', 'vehicle_type'],
            ]);

        $this->assertDatabaseHas('vehicles', [
            'plate' => 'ABC123',
            'owner_name' => 'Juan Pérez',
        ]);
    }

    public function test_cannot_create_vehicle_with_duplicate_plate(): void
    {
        // Arrange
        $token = $this->authenticate();
        Vehicle::factory()->create(['plate' => 'ABC123']);

        $vehicleData = [
            'plate' => 'ABC123',
            'owner_name' => 'Juan Pérez',
            'phone' => '3001234567',
            'vehicle_type' => 'car',
        ];

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vehicles', $vehicleData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plate']);
    }

    public function test_can_get_vehicle_by_id(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicle = Vehicle::factory()->create();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/vehicles/{$vehicle->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'plate', 'owner_name', 'phone', 'vehicle_type'],
            ]);

        $this->assertEquals($vehicle->id, $response->json('data.id'));
    }

    public function test_can_search_vehicle_by_plate(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicle = Vehicle::factory()->create(['plate' => 'XYZ789']);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/vehicles/search/plate?plate=XYZ789');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'plate' => 'XYZ789',
                ],
            ]);
    }

    public function test_returns_404_when_vehicle_not_found(): void
    {
        // Arrange
        $token = $this->authenticate();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/vehicles/99999');

        // Assert
        $response->assertStatus(404);
    }

    public function test_validates_required_fields_when_creating_vehicle(): void
    {
        // Arrange
        $token = $this->authenticate();

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/vehicles', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['plate', 'owner_name', 'phone', 'vehicle_type']);
    }
}





