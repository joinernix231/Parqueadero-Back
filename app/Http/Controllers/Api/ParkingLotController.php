<?php

namespace App\Http\Controllers\Api;

use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Http\Requests\ParkingLot\StoreParkingLotRequest;
use App\Http\Requests\ParkingLot\UpdateParkingLotRequest;
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

    public function store(StoreParkingLotRequest $request): JsonResponse|ParkingLotResource
    {
        try {
            $data = $request->validated();
            $lot = $this->parkingLotRepository->create($data);
            return new ParkingLotResource($lot);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
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

    public function update(UpdateParkingLotRequest $request, int $id): JsonResponse|ParkingLotResource
    {
        try {
            $lot = $this->parkingLotRepository->findById($id);
            if (!$lot) {
                return response()->json([
                    'message' => 'Estacionamiento no encontrado',
                ], 404);
            }

            $data = $request->validated();
            $updated = $this->parkingLotRepository->update($id, $data);
            
            if (!$updated) {
                return response()->json([
                    'message' => 'Error al actualizar el estacionamiento',
                ], 500);
            }

            // Obtener el estacionamiento actualizado
            $updatedLot = $this->parkingLotRepository->findById($id);
            return new ParkingLotResource($updatedLot);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}




