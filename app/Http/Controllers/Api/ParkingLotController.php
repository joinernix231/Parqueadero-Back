<?php

namespace App\Http\Controllers\Api;

use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Http\Resources\ParkingLotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParkingLotController extends Controller
{
    public function __construct(
        private ParkingLotRepositoryInterface $parkingLotRepository
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['is_active']);
            $lots = $this->parkingLotRepository->all($filters);
            return response()->json([
                'data' => ParkingLotResource::collection($lots),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse|ParkingLotResource
    {
        try {
            $lot = $this->parkingLotRepository->findById($id);
            if (!$lot) {
                return response()->json([
                    'message' => 'Estacionamiento no encontrado',
                ], 404);
            }
            return new ParkingLotResource($lot);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}




