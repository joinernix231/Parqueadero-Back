<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Parking\ExitVehicleUseCase;
use App\Domain\DTOs\ExitVehicleDTO;
use App\Http\Requests\Parking\ExitRequest;
use App\Http\Resources\ParkingTicketResource;
use Illuminate\Http\JsonResponse;
use Throwable;

class ParkingExitController extends Controller
{
    public function __construct(private ExitVehicleUseCase $exitVehicleUseCase) {}

    public function store(ExitRequest $request): JsonResponse
    {
        try {
            $dto = ExitVehicleDTO::fromArray($request->validated());
            $ticket = $this->exitVehicleUseCase->execute($dto, $request->user()->id);

            return $this->sendResponse(new ParkingTicketResource($ticket), 'Exit registered successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }
}
