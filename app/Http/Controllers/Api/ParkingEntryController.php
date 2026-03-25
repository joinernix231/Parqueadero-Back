<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Parking\EntryVehicleUseCase;
use App\Domain\DTOs\EntryVehicleDTO;
use App\Http\Requests\Parking\EntryRequest;
use App\Http\Resources\ParkingTicketResource;
use Illuminate\Http\JsonResponse;
use Throwable;

class ParkingEntryController extends Controller
{
    public function __construct(private EntryVehicleUseCase $entryVehicleUseCase) {}

    public function store(EntryRequest $request): JsonResponse
    {
        try {
            $dto = EntryVehicleDTO::fromArray($request->validated());
            $ticket = $this->entryVehicleUseCase->execute($dto, $request->user()->id);
            return $this->sendResponse(new ParkingTicketResource($ticket), 'Ingreso registrado correctamente');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }
}
