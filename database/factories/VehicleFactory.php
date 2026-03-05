<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->bothify('???###')),
            'owner_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'vehicle_type' => $this->faker->randomElement(['car', 'motorcycle', 'truck']),
        ];
    }
}




