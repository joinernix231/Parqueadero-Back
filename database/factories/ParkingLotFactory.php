<?php

namespace Database\Factories;

use App\Models\ParkingLot;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingLotFactory extends Factory
{
    protected $model = ParkingLot::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Estacionamiento',
            'address' => $this->faker->address(),
            'total_spots' => $this->faker->numberBetween(20, 100),
            'hourly_rate_day' => $this->faker->randomFloat(2, 2.0, 5.0),
            'hourly_rate_night' => $this->faker->randomFloat(2, 2.5, 6.0),
            'day_start_time' => '06:00',
            'day_end_time' => '20:00',
            'is_active' => true,
        ];
    }
}




