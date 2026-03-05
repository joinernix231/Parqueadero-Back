<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Parking\EntryVehicleAction;
use App\Application\Actions\Parking\ExitVehicleAction;
use App\Application\Actions\Parking\GetCurrentVehiclesAction;
use App\Application\Actions\Parking\GetHistoryAction;
use App\Application\Actions\Parking\ProcessPaymentAction;
use App\Application\Services\ReceiptService;
use App\Domain\DTOs\EntryVehicleDTO;
use App\Domain\DTOs\ExitVehicleDTO;
use App\Domain\DTOs\PaymentDTO;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Services\PricingService;
use App\Http\Requests\Parking\EntryRequest;
use App\Http\Requests\Parking\ExitRequest;
use App\Http\Requests\Parking\HistoryRequest;
use App\Http\Requests\Parking\PaymentRequest;
use App\Http\Resources\ParkingTicketResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParkingController extends Controller
{
    public function __construct(
        private EntryVehicleAction $entryVehicleAction,
        private ExitVehicleAction $exitVehicleAction,
        private ProcessPaymentAction $processPaymentAction,
        private GetCurrentVehiclesAction $getCurrentVehiclesAction,
        private GetHistoryAction $getHistoryAction,
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private ReceiptService $receiptService,
        private PricingService $pricingService,
        private ParkingLotRepositoryInterface $parkingLotRepository
    ) {
    }

    public function storeEntry(EntryRequest $request): JsonResponse|ParkingTicketResource
    {
        try {
            $dto = EntryVehicleDTO::fromArray($request->validated());
            $guardId = $request->user()->id;
            $ticket = $this->entryVehicleAction->execute($dto, $guardId);
            return new ParkingTicketResource($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeExit(ExitRequest $request): JsonResponse|ParkingTicketResource
    {
        try {
            $dto = ExitVehicleDTO::fromArray($request->validated());
            $guardId = $request->user()->id;
            $ticket = $this->exitVehicleAction->execute($dto, $guardId);
            return new ParkingTicketResource($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(int $id): JsonResponse|ParkingTicketResource
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket no encontrado',
                ], 404);
            }
            return new ParkingTicketResource($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function findByPlate(string $plate): JsonResponse|ParkingTicketResource
    {
        try {
            $ticket = $this->parkingTicketRepository->findActiveByPlate($plate);
            if (!$ticket) {
                return response()->json([
                    'message' => 'No se encontró un ticket activo para la placa proporcionada',
                ], 404);
            }
            return new ParkingTicketResource($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function processPayment(PaymentRequest $request): JsonResponse|ParkingTicketResource
    {
        try {
            $dto = PaymentDTO::fromArray($request->validated());
            $ticket = $this->processPaymentAction->execute($dto);
            return new ParkingTicketResource($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function current(Request $request): JsonResponse
    {
        try {
            $parkingLotId = $request->get('parking_lot_id') ? (int)$request->get('parking_lot_id') : null;
            $tickets = $this->getCurrentVehiclesAction->execute($parkingLotId);
            
            // Asegurar que los tickets tengan la información del vehículo cargada
            $ticketsWithRelations = collect($tickets)->map(function ($ticket) {
                // Recargar el ticket con relaciones para asegurar que el resource tenga acceso
                return $this->parkingTicketRepository->findById($ticket->getId());
            })->filter();
            
            return response()->json([
                'data' => ParkingTicketResource::collection($ticketsWithRelations),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function history(HistoryRequest $request): JsonResponse
    {
        try {
            // Obtener filtros del query string o del body
            $filters = $request->get('filters') ?? $request->only(['date_from', 'date_to', 'plate', 'parking_lot_id', 'status']);
            $perPage = $request->get('per_page', 15);
            $paginator = $this->getHistoryAction->execute($filters, true, $perPage);
            
            // Convertir modelos a entidades del dominio con relaciones cargadas
            $tickets = collect($paginator->items())->map(function ($model) {
                // El modelo ya tiene las relaciones cargadas, usar findById que carga relaciones
                return $this->parkingTicketRepository->findById($model->id);
            })->filter();
            
            return response()->json([
                'data' => ParkingTicketResource::collection($tickets),
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

    /**
     * Descarga el recibo PDF de entrada
     */
    public function downloadEntryReceipt(int $id): Response|JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket no encontrado',
                ], 404);
            }

            // Generar el PDF de forma síncrona
            return $this->receiptService->generateEntryReceipt($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Descarga el recibo PDF de salida
     */
    public function downloadExitReceipt(int $id): Response|JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket no encontrado',
                ], 404);
            }

            if (!$ticket->getExitTime()) {
                return response()->json([
                    'message' => 'El ticket aún no tiene salida registrada',
                ], 422);
            }

            // Generar el PDF de forma síncrona
            return $this->receiptService->generateExitReceipt($ticket);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calcula el precio antes de registrar la salida
     */
    public function calculatePrice(int $id): JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket no encontrado',
                ], 404);
            }

            if (!$ticket->isActive()) {
                return response()->json([
                    'message' => 'El ticket ya tiene salida registrada',
                ], 422);
            }

            // Obtener el estacionamiento para calcular el precio
            $parkingLot = $this->parkingLotRepository->findById($ticket->getParkingLotId());
            if (!$parkingLot) {
                return response()->json([
                    'message' => 'Estacionamiento no encontrado',
                ], 404);
            }

            // Calcular horas transcurridas hasta ahora
            $totalHours = $this->pricingService->calculateTotalHours($ticket);

            // Crear un ticket temporal con la hora de salida actual para calcular el precio
            // Usar el método calculatePrice directamente con el tiempo actual
            $exitTime = date('Y-m-d H:i:s');
            $entryTimestamp = strtotime($ticket->getEntryTime());
            $exitTimestamp = strtotime($exitTime);
            $totalSeconds = $exitTimestamp - $entryTimestamp;
            $totalHours = $totalSeconds / 3600;

            // Calcular precio usando el método del ticket pero con tiempo actual
            $price = 0.0;
            $currentTimestamp = $entryTimestamp;

            while ($currentTimestamp < $exitTimestamp) {
                $currentTime = date('H:i:s', $currentTimestamp);
                $isDayTime = $parkingLot->isDayTime($currentTime);

                // Calcular horas hasta el próximo cambio o hasta la salida
                $nextChangeTimestamp = $this->getNextRateChangeTimestamp($currentTimestamp, $parkingLot);
                $segmentEndTimestamp = min($nextChangeTimestamp, $exitTimestamp);
                $segmentHours = ($segmentEndTimestamp - $currentTimestamp) / 3600;

                $rate = $isDayTime ? $parkingLot->getHourlyRateDay() : $parkingLot->getHourlyRateNight();
                $price += $segmentHours * $rate;

                $currentTimestamp = $segmentEndTimestamp;
            }

            $totalAmount = round($price, 2);

            // Obtener la tarifa aplicada (diurna o nocturna según la hora actual)
            $currentTime = date('H:i:s');
            $hourlyRateApplied = $parkingLot->isDayTime($currentTime) 
                ? $parkingLot->getHourlyRateDay() 
                : $parkingLot->getHourlyRateNight();

            return response()->json([
                'data' => [
                    'ticket_id' => $ticket->getId(),
                    'total_hours' => round($totalHours, 2),
                    'hourly_rate_applied' => $hourlyRateApplied,
                    'total_amount' => round($totalAmount, 2),
                    'entry_time' => $ticket->getEntryTime(),
                    'calculated_at' => date('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene el timestamp del próximo cambio de tarifa
     */
    private function getNextRateChangeTimestamp(int $currentTimestamp, \App\Domain\Entities\ParkingLot $lot): int
    {
        $currentDate = date('Y-m-d', $currentTimestamp);
        $dayStartTimestamp = strtotime($currentDate . ' ' . $lot->getDayStartTime());
        $dayEndTimestamp = strtotime($currentDate . ' ' . $lot->getDayEndTime());

        // Si dayEnd es antes de dayStart, significa que cruza medianoche
        if ($dayEndTimestamp < $dayStartTimestamp) {
            $dayEndTimestamp = strtotime($currentDate . ' ' . $lot->getDayEndTime() . ' +1 day');
        }

        $isCurrentlyDayTime = $lot->isDayTime(date('H:i:s', $currentTimestamp));

        if ($isCurrentlyDayTime) {
            return $dayEndTimestamp;
        }

        // Si es noche, el próximo cambio es al inicio del día siguiente
        $nextDayStart = strtotime($currentDate . ' ' . $lot->getDayStartTime() . ' +1 day');
        return $nextDayStart;
    }
}

