<?php

namespace Database\Seeders;

use App\Models\ParkingLot;
use Illuminate\Database\Seeder;

class ParkingLotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParkingLot::create([
            'name' => 'Estacionamiento Central',
            'address' => 'Av. Principal 123',
            'total_spots' => 50,
            'hourly_rate_day' => 2.50,
            'hourly_rate_night' => 3.00,
            'day_start_time' => '06:00',
            'day_end_time' => '20:00',
            'is_active' => true,
        ]);

        ParkingLot::create([
            'name' => 'Estacionamiento Norte',
            'address' => 'Calle Norte 456',
            'total_spots' => 30,
            'hourly_rate_day' => 2.00,
            'hourly_rate_night' => 2.50,
            'day_start_time' => '07:00',
            'day_end_time' => '19:00',
            'is_active' => true,
        ]);
    }
}




