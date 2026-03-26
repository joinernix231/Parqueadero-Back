<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\ParkingLot\GetAvailableSpotsUseCase;
use App\Http\Resources\ParkingSpotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ParkingSpotController extends Controller
{
    public function __construct(
        private GetAvailableSpotsUseCase $getAvailableSpotsUseCase
    ) {}

    public function available(Request $request): JsonResponse
    {
        try {
            $parkingLotId = $request->get('parking_lot_id');
            if (! $parkingLotId) {
                return $this->sendError(
                    'parking_lot_id is required',
                    422
                );
            }

            $spots = $this->getAvailableSpotsUseCase->execute($parkingLotId);

            return $this->sendResponse(
                ParkingSpotResource::collection($spots),
                'Available spots retrieved successfully'
            );
        } catch (Throwable $e) {
            return $this->sendError(
                $e->getMessage(),
                500
            );
        }
    }
}
