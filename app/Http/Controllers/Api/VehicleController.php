<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Vehicle\FindVehicleByPlateUseCase;
use App\Application\UseCases\Vehicle\RegisterVehicleUseCase;
use App\Domain\DTOs\VehicleDTO;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class VehicleController extends Controller
{
    public function __construct(
        private RegisterVehicleUseCase $registerVehicleUseCase,
        private FindVehicleByPlateUseCase $findVehicleByPlateUseCase,
        private VehicleRepositoryInterface $vehicleRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->except(['per_page', 'page']);
            $perPage = (int) $request->get('per_page', 15);
            $paginator = $this->vehicleRepository->paginate($perPage, $filters);

            return $this->sendResponse(
                VehicleResource::collection(collect($paginator->items())),
                'Vehicles retrieved successfully',
                [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ]
            );
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        try {
            $dto = VehicleDTO::fromArray($request->validated());
            $vehicle = $this->registerVehicleUseCase->execute($dto);

            return $this->sendResponse(new VehicleResource($vehicle), 'Vehicle created successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $vehicle = $this->vehicleRepository->findById($id);
            if (! $vehicle) {
                return $this->sendError('Vehicle not found', 404);
            }

            return $this->sendResponse(new VehicleResource($vehicle), 'Vehicle retrieved successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function searchByPlate(Request $request): JsonResponse
    {
        try {
            $plate = $request->get('plate');
            if (! $plate) {
                return $this->sendError('License plate is required', 422);
            }

            $vehicle = $this->findVehicleByPlateUseCase->execute($plate);
            if (! $vehicle) {
                return $this->sendError('Vehicle not found', 404);
            }

            return $this->sendResponse(new VehicleResource($vehicle), 'Vehicle retrieved successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
