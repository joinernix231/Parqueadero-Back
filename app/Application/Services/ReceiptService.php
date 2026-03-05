<?php

namespace App\Application\Services;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use Illuminate\Http\Response;

class ReceiptService
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
        private ParkingLotRepositoryInterface $parkingLotRepository,
        private ParkingSpotRepositoryInterface $parkingSpotRepository
    ) {
    }

    /**
     * Genera un recibo PDF de entrada y lo descarga
     */
    public function generateEntryReceipt(ParkingTicket $ticket): Response
    {
        $data = $this->prepareReceiptData($ticket);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.entry-receipt', $data);
        
        return $pdf->download('recibo-entrada-' . $ticket->getId() . '.pdf');
    }

    /**
     * Genera un recibo PDF de entrada y retorna el contenido del PDF (para Jobs)
     */
    public function generateEntryReceiptPdf(ParkingTicket $ticket): string
    {
        $data = $this->prepareReceiptData($ticket);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.entry-receipt', $data);
        
        return $pdf->output();
    }

    /**
     * Genera un recibo PDF de salida y lo descarga
     */
    public function generateExitReceipt(ParkingTicket $ticket): Response
    {
        if (!$ticket->getExitTime()) {
            throw new \Exception('No se puede generar recibo de salida sin hora de salida registrada');
        }

        $data = $this->prepareReceiptData($ticket);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.exit-receipt', $data);
        
        return $pdf->download('recibo-salida-' . $ticket->getId() . '.pdf');
    }

    /**
     * Genera un recibo PDF de salida y retorna el contenido del PDF (para Jobs)
     */
    public function generateExitReceiptPdf(ParkingTicket $ticket): string
    {
        if (!$ticket->getExitTime()) {
            throw new \Exception('No se puede generar recibo de salida sin hora de salida registrada');
        }

        $data = $this->prepareReceiptData($ticket);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.exit-receipt', $data);
        
        return $pdf->output();
    }

    /**
     * Prepara los datos necesarios para generar el recibo
     */
    private function prepareReceiptData(ParkingTicket $ticket): array
    {
        $vehicle = $this->vehicleRepository->findById($ticket->getVehicleId());
        $parkingLot = $this->parkingLotRepository->findById($ticket->getParkingLotId());
        $parkingSpot = $this->parkingSpotRepository->findById($ticket->getParkingSpotId());

        if (!$vehicle || !$parkingLot) {
            throw new \Exception('No se pudo obtener la información completa del ticket');
        }

        return [
            'ticket' => $ticket,
            'vehicle' => $vehicle,
            'parkingLot' => $parkingLot,
            'parkingSpot' => $parkingSpot,
            'entryTime' => $this->formatDateTime($ticket->getEntryTime()),
            'exitTime' => $ticket->getExitTime() ? $this->formatDateTime($ticket->getExitTime()) : null,
            'totalHours' => $ticket->getTotalHours(),
            'totalAmount' => $ticket->getTotalAmount(),
            'hourlyRateApplied' => $ticket->getHourlyRateApplied(),
            'isPaid' => $ticket->isPaid(),
            'paymentMethod' => $ticket->getPaymentMethod(),
            'paymentTime' => $ticket->getPaymentTime() ? $this->formatDateTime($ticket->getPaymentTime()) : null,
        ];
    }

    /**
     * Formatea una fecha/hora para mostrar en el recibo
     */
    private function formatDateTime(string $dateTime): string
    {
        try {
            $date = new \DateTime($dateTime);
            return $date->format('d/m/Y H:i:s');
        } catch (\Exception $e) {
            return $dateTime;
        }
    }
}

