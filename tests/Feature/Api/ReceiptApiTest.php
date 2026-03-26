<?php

namespace Tests\Feature\Api;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use App\Models\ParkingTicket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptApiTest extends TestCase
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

    public function test_can_download_entry_receipt(): void
    {
        $token = $this->authenticate();
        $user = User::first();
        $vehicle = Vehicle::factory()->create();
        $parkingLot = ParkingLot::factory()->create();
        $parkingSpot = ParkingSpot::factory()->create([
            'parking_lot_id' => $parkingLot->id,
            'is_occupied' => false,
        ]);

        $ticket = ParkingTicket::factory()->create([
            'vehicle_id' => $vehicle->id,
            'parking_lot_id' => $parkingLot->id,
            'parking_spot_id' => $parkingSpot->id,
            'entry_guard_id' => $user->id,
            'exit_time' => null,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get("/api/parking/tickets/{$ticket->id}/receipt/entry");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_can_download_exit_receipt(): void
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
            'exit_guard_id' => $user->id,
            'exit_time' => now(),
            'total_hours' => 2.5,
            'total_amount' => 5000.00,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get("/api/parking/tickets/{$ticket->id}/receipt/exit");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_cannot_download_exit_receipt_without_exit_time(): void
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
            'exit_time' => null,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get("/api/parking/tickets/{$ticket->id}/receipt/exit");

        $response->assertStatus(422);
    }

    public function test_requires_authentication_to_download_receipt(): void
    {
        $ticket = ParkingTicket::factory()->create();

        $response = $this->getJson("/api/parking/tickets/{$ticket->id}/receipt/entry");

        $response->assertStatus(401);
    }

    public function test_returns_404_for_nonexistent_ticket(): void
    {
        $token = $this->authenticate();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get('/api/parking/tickets/99999/receipt/entry');

        $response->assertStatus(404);
    }
}
