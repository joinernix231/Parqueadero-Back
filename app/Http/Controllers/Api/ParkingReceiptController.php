<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\ReceiptService;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class ParkingReceiptController extends Controller
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private ReceiptService $receiptService
    ) {}

    public function downloadEntry(int $id): Response|JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (! $ticket) {
                return $this->sendError('Ticket not found', 404);
            }

            return $this->receiptService->generateEntryReceipt($ticket);
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function downloadExit(int $id): Response|JsonResponse
    {
        try {
            $ticket = $this->parkingTicketRepository->findById($id);
            if (! $ticket) {
                return $this->sendError('Ticket not found', 404);
            }
            if (! $ticket->getExitTime()) {
                return $this->sendError('This ticket does not have an exit time recorded yet', 422);
            }

            return $this->receiptService->generateExitReceipt($ticket);
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
