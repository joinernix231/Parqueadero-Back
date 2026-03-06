<?php

namespace Database\Seeders;

use App\Models\ParkingLot;
use App\Models\ParkingSpot;
use Illuminate\Database\Seeder;

class ParkingSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parkingLots = ParkingLot::all();

        foreach ($parkingLots as $lot) {
            // Crear espacios regulares
            for ($i = 1; $i <= $lot->total_spots - 3; $i++) {
                ParkingSpot::create([
                    'parking_lot_id' => $lot->id,
                    'spot_number' => 'R' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'spot_type' => 'regular',
                    'is_occupied' => false,
                    'is_active' => true,
                ]);
            }

            // Crear espacio para discapacitados
            ParkingSpot::create([
                'parking_lot_id' => $lot->id,
                'spot_number' => 'D001',
                'spot_type' => 'disabled',
                'is_occupied' => false,
                'is_active' => true,
            ]);

            // Crear espacios VIP
            ParkingSpot::create([
                'parking_lot_id' => $lot->id,
                'spot_number' => 'V001',
                'spot_type' => 'vip',
                'is_occupied' => false,
                'is_active' => true,
            ]);

            ParkingSpot::create([
                'parking_lot_id' => $lot->id,
                'spot_number' => 'V002',
                'spot_type' => 'vip',
                'is_occupied' => false,
                'is_active' => true,
            ]);
        }
    }
}





