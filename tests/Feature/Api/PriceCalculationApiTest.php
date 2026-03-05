<?php

namespace Tests\Feature\Api;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use App\Models\ParkingTicket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceCalculationApiTest extends TestCase
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

    public function test_can_calculate_price_before_exit(): void
    {
        $token = $this->authenticate();
        $user = User::first();
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create([
            'hourly_rate_day' => 2000.00,
            'hourly_rate_night' => 3000.00,
        ]);
        $parkingSpot = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
        ]);

        // Crear ticket con entrada hace 2 horas
        $entryTime = now()->subHours(2);
        $ticket = ParkingTicket::factory()->create([
            'vehicle_id' => $vehicle->id,
            'parking_lot_id' => $parkingLot->id,
            'parking_spot_id' => $parkingSpot->id,
            'entry_guard_id' => $user->id,
            'entry_time' => $entryTime,
            'exit_time' => null,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/parking/tickets/{$ticket->id}/calculate-price");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'ticket_id',
                'total_hours',
                'hourly_rate_applied',
                'total_amount',
                'entry_time',
                'calculated_at',
            ],
        ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['total_hours']);
        $this->assertGreaterThan(0, $data['total_amount']);
    }

    public function test_cannot_calculate_price_for_closed_ticket(): void
    {
        $token = $this->authenticate();
        $user = User::first();
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $parkingSpot = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
        ]);

        $ticket = ParkingTicket::factory()->create([
            'vehicle_id' => $vehicle->id,
            'parking_lot_id' => $parkingLot->id,
            'parking_spot_id' => $parkingSpot->id,
            'entry_guard_id' => $user->id,
            'exit_time' => now(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/parking/tickets/{$ticket->id}/calculate-price");

        $response->assertStatus(422);
    }

    public function test_returns_404_for_nonexistent_ticket(): void
    {
        $token = $this->authenticate();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/parking/tickets/99999/calculate-price');

        $response->assertStatus(404);
    }

    public function test_requires_authentication_to_calculate_price(): void
    {
        $ticket = ParkingTicket::factory()->create();

        $response = $this->getJson("/api/parking/tickets/{$ticket->id}/calculate-price");

        $response->assertStatus(401);
    }
}

