<?php

namespace Database\Factories;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingSpotFactory extends Factory
{
    protected $model = ParkingSpot::class;

    public function definition(): array
    {
        return [
            'parking_lot_id' => ParkingLot::factory(),
            'spot_number' => $this->faker->unique()->bothify('R###'),
            'spot_type' => $this->faker->randomElement(['regular', 'disabled', 'vip']),
            'is_occupied' => false,
            'is_active' => true,
        ];
    }
}
