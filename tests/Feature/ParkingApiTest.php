<?php

namespace Tests\Feature;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingApiTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): string
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'guard',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $response->json('data.token');
    }

    public function test_can_register_vehicle_entry(): void
    {
        // Arrange
        $token = $this->authenticate();
        $user = User::where('email', User::factory()->make()->email)->first() ?? User::factory()->create(['role' => 'guard']);
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $parkingSpot = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $parkingSpot->id,
            ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'vehicle_id',
                    'parking_spot_id',
                    'parking_lot_id',
                    'entry_time',
                ],
            ]);

        $this->assertDatabaseHas('parking_tickets', [
            'vehicle_id' => $vehicle->id,
            'parking_spot_id' => $parkingSpot->id,
        ]);

        $this->assertDatabaseHas('parking_spots', [
            'id' => $parkingSpot->id,
            'is_occupied' => true,
        ]);
    }

    public function test_cannot_register_entry_if_vehicle_already_parked(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $parkingSpot1 = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);
        $parkingSpot2 = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);

        // Crear entrada activa
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $parkingSpot1->id,
            ]);

        // Act - Intentar segunda entrada
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $parkingSpot2->id,
            ]);

        // Assert
        $response->assertStatus(422);
    }

    public function test_can_register_vehicle_exit(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $parkingSpot = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);

        // Registrar entrada
        $entryResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $parkingSpot->id,
            ]);

        $ticketId = $entryResponse->json('data.id');

        // Act - Registrar salida
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/exit', [
                'ticket_id' => $ticketId,
            ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'exit_time',
                    'total_hours',
                    'total_amount',
                ],
            ]);

        $this->assertNotNull($response->json('data.exit_time'));
        $this->assertDatabaseHas('parking_spots', [
            'id' => $parkingSpot->id,
            'is_occupied' => false,
        ]);
    }

    public function test_can_get_current_parked_vehicles(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicle1 = Vehicle::factory()->create();
        $vehicle2 = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $spot1 = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);
        $spot2 = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);

        // Crear entradas
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle1->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $spot1->id,
            ]);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle2->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $spot2->id,
            ]);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/parking/current');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_get_parking_history(): void
    {
        // Arrange
        $token = $this->authenticate();
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $parkingSpot = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);

        // Crear entrada y salida
        $entryResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/entry', [
                'vehicle_id' => $vehicle->id,
                'parking_lot_id' => $parkingLot->id,
                'parking_spot_id' => $parkingSpot->id,
            ]);

        $ticketId = $entryResponse->json('data.id');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/parking/exit', [
                'ticket_id' => $ticketId,
            ]);

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/parking/history');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'vehicle_id', 'entry_time', 'exit_time'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }
}




