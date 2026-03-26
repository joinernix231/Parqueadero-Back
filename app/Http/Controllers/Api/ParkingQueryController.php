<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Parking\GetCurrentParkedVehiclesUseCase;
use App\Application\UseCases\Parking\GetParkingHistoryUseCase;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Http\Requests\Parking\CurrentTicketsRequest;
use App\Http\Requests\Parking\HistoryRequest;
use App\Http\Resources\ParkingTicketResource;
use Illuminate\Http\JsonResponse;
use Throwable;

class ParkingQueryController extends Controller
{
    public function __construct(
        private GetCurrentParkedVehiclesUseCase $getCurrentParkedVehiclesUseCase,
        private GetParkingHistoryUseCase $getParkingHistoryUseCase,
        private ParkingTicketRepositoryInterface $parkingTicketRepository
    ) {}

    public function show(int $id): JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (! $ticket) {
                return $this->sendError('Ticket not found', 404);
            }

            return $this->sendResponse(new ParkingTicketResource($ticket), 'Ticket retrieved successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function findByPlate(string $plate): JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findActiveByPlate($plate);
            if (! $ticket) {
                return $this->sendError('No active ticket found for the given license plate', 404);
            }

            return $this->sendResponse(new ParkingTicketResource($ticket), 'Active ticket found successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function current(CurrentTicketsRequest $request): JsonResponse
    {
        try {
            $result = $this->getCurrentParkedVehiclesUseCase->execute(
                $request->getParkingLotId(),
                $request->getFilters(),
                $request->getSearch(),
                $request->shouldPaginate(),
                $request->getPerPage()
            );

            if ($request->shouldPaginate()) {
                return $this->sendResponse(
                    ParkingTicketResource::collection(collect($result->items())),
                    'Current tickets retrieved successfully',
                    [
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'per_page' => $result->perPage(),
                        'total' => $result->total(),
                    ]
                );
            }

            return $this->sendResponse(
                ParkingTicketResource::collection(collect($result)),
                'Current tickets retrieved successfully'
            );
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function history(HistoryRequest $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'plate', 'parking_lot_id', 'status']);
            $perPage = (int) $request->get('per_page', 15);
            $paginator = $this->getParkingHistoryUseCase->execute($filters, true, $perPage);

            return $this->sendResponse(
                ParkingTicketResource::collection(collect($paginator->items())),
                'History retrieved successfully',
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
}
