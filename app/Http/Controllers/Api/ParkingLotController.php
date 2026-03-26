<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\ParkingLot\CreateParkingLotUseCase;
use App\Application\UseCases\ParkingLot\UpdateParkingLotUseCase;
use App\Domain\DTOs\ParkingLotDTO;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Http\Requests\ParkingLot\StoreParkingLotRequest;
use App\Http\Requests\ParkingLot\UpdateParkingLotRequest;
use App\Http\Resources\ParkingLotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ParkingLotController extends Controller
{
    public function __construct(
        private ParkingLotRepositoryInterface $parkingLotRepository,
        private CreateParkingLotUseCase $createParkingLotUseCase,
        private UpdateParkingLotUseCase $updateParkingLotUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $lots = $this->parkingLotRepository->all($request->only(['is_active']));

            return $this->sendResponse(ParkingLotResource::collection($lots), 'Parking lots retrieved successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $lot = $this->parkingLotRepository->findById($id);
            if (! $lot) {
                return $this->sendError('Parking lot not found', 404);
            }

            return $this->sendResponse(new ParkingLotResource($lot), 'Parking lot retrieved successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function store(StoreParkingLotRequest $request): JsonResponse
    {
        try {
            $dto = ParkingLotDTO::fromArray($request->validated());
            $lot = $this->createParkingLotUseCase->execute($dto);

            return $this->sendResponse(new ParkingLotResource($lot), 'Parking lot created successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function update(UpdateParkingLotRequest $request, int $id): JsonResponse
    {
        try {
            $lot = $this->updateParkingLotUseCase->execute($id, $request->validated());

            return $this->sendResponse(new ParkingLotResource($lot), 'Parking lot updated successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }
}
