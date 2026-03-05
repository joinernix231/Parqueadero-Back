<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\ParkingLot\GetAvailableSpotsUseCase;
use App\Http\Resources\ParkingSpotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParkingSpotController extends Controller
{
    public function __construct(
        private GetAvailableSpotsUseCase $getAvailableSpotsUseCase
    ) {
    }

    public function available(Request $request): JsonResponse
    {
        try {
            $parkingLotId = $request->get('parking_lot_id');
            if (!$parkingLotId) {
                return response()->json([
                    'message' => 'parking_lot_id es requerido',
                ], 422);
            }

            $spots = $this->getAvailableSpotsUseCase->execute($parkingLotId);
            return response()->json([
                'data' => ParkingSpotResource::collection($spots),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}




