<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Parking\ProcessPaymentUseCase;
use App\Domain\DTOs\PaymentDTO;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Services\PricingService;
use App\Http\Requests\Parking\PaymentRequest;
use App\Http\Resources\ParkingTicketResource;
use Illuminate\Http\JsonResponse;
use Throwable;

class ParkingPaymentController extends Controller
{
    public function __construct(
        private ProcessPaymentUseCase $processPaymentUseCase,
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private ParkingLotRepositoryInterface $parkingLotRepository,
        private PricingService $pricingService
    ) {
    }

    public function process(PaymentRequest $request): JsonResponse
    {
        try {
            $dto = PaymentDTO::fromArray($request->validated());
            $ticket = $this->processPaymentUseCase->execute($dto);
            return $this->sendResponse(new ParkingTicketResource($ticket), 'Pago procesado correctamente');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function calculatePrice(int $id): JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (!$ticket) {
                return $this->sendError('Ticket no encontrado', 404);
            }
            if (!$ticket->isActive()) {
                return $this->sendError('El ticket ya tiene salida registrada', 422);
            }

            $parkingLot = $ticket->getParkingLot()
                ?? $this->parkingLotRepository->findById($ticket->getParkingLotId());

            if (!$parkingLot) {
                return $this->sendError('Estacionamiento no encontrado', 404);
            }

            $now = now()->format('Y-m-d H:i:s');
            $nowHour = now()->format('H:i');
            $tempTicket = clone $ticket;
            $tempTicket->setExitTime($now);

            return $this->sendResponse(
                [
                    'ticket_id'           => $ticket->getId(),
                    'total_hours'         => $this->pricingService->calculateTotalHours($tempTicket),
                    'hourly_rate_applied' => $parkingLot->isDayTime($nowHour)
                        ? $parkingLot->getHourlyRateDay()
                        : $parkingLot->getHourlyRateNight(),
                    'total_amount'        => $this->pricingService->calculatePrice($tempTicket, $parkingLot),
                    'entry_time'          => $ticket->getEntryTime(),
                    'calculated_at'       => $now,
                ],
                'Precio calculado correctamente'
            );
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
