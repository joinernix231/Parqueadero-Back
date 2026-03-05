<?php

namespace Database\Factories;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use App\Models\ParkingTicket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingTicketFactory extends Factory
{
    protected $model = ParkingTicket::class;

    public function definition(): array
    {
        $entryTime = $this->faker->dateTimeBetween('-1 day', 'now');
        $exitTime = $this->faker->optional(0.5)->dateTimeBetween($entryTime, 'now');

        return [
            'vehicle_id' => Vehicle::factory(),
            'parking_spot_id' => ParkingSpot::factory(),
            'parking_lot_id' => ParkingLot::factory(),
            'entry_time' => $entryTime,
            'exit_time' => $exitTime,
            'entry_guard_id' => User::factory(),
            'exit_guard_id' => $exitTime ? User::factory() : null,
            'total_hours' => $exitTime ? round((strtotime($exitTime->format('Y-m-d H:i:s')) - strtotime($entryTime->format('Y-m-d H:i:s'))) / 3600, 2) : 0,
            'hourly_rate_applied' => $this->faker->randomFloat(2, 2.0, 5.0),
            'total_amount' => $this->faker->randomFloat(2, 5.0, 50.0),
            'is_paid' => $exitTime ? $this->faker->boolean(70) : false,
            'payment_method' => function (array $attributes) {
                return $attributes['is_paid'] ? $this->faker->randomElement(['cash', 'card', 'transfer']) : null;
            },
            'payment_time' => function (array $attributes) {
                return $attributes['is_paid'] ? $this->faker->dateTimeBetween($attributes['exit_time'], 'now') : null;
            },
        ];
    }
}




