<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Vehicle\CreateVehicleAction;
use App\Application\UseCases\Vehicle\FindVehicleByPlateUseCase;
use App\Domain\DTOs\VehicleDTO;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    public function __construct(
        private CreateVehicleAction $createVehicleAction,
        private FindVehicleByPlateUseCase $findVehicleByPlateUseCase,
        private VehicleRepositoryInterface $vehicleRepository
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            // Obtener filtros del query string o del body
            $filters = $request->get('filters') ?? $request->except(['per_page', 'page']);
            $perPage = $request->get('per_page', 15);
            
            $paginator = $this->vehicleRepository->paginate($perPage, $filters);
            
            // Convertir modelos a entidades del dominio
            $vehicles = collect($paginator->items())->map(function ($model) {
                return $this->vehicleRepository->findById($model->id);
            })->filter();
            
            return response()->json([
                'data' => VehicleResource::collection($vehicles),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreVehicleRequest $request): JsonResponse|VehicleResource
    {
        try {
            $dto = VehicleDTO::fromArray($request->validated());
            $vehicle = $this->createVehicleAction->execute($dto);
            return new VehicleResource($vehicle);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(int $id): JsonResponse|VehicleResource
    {
        try {
            $vehicle = $this->vehicleRepository->findById($id);
            if (!$vehicle) {
                return response()->json([
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }
            return new VehicleResource($vehicle);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchByPlate(Request $request): JsonResponse
    {
        try {
            $plate = $request->get('plate');
            if (!$plate) {
                return response()->json([
                    'message' => 'La placa es requerida',
                ], 422);
            }

            $vehicle = $this->findVehicleByPlateUseCase->execute($plate);
            if (!$vehicle) {
                return response()->json([
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }

            $resource = new VehicleResource($vehicle);
            return response()->json([
                'data' => $resource->toArray($request),
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching vehicle by plate: ' . $e->getMessage(), [
                'plate' => $request->get('plate'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al buscar vehículo: ' . $e->getMessage(),
            ], 500);
        }
    }
}

