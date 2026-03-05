<?php

namespace App\Jobs;

use App\Application\Services\ReceiptService;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateEntryReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $ticketId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        ReceiptService $receiptService,
        ParkingTicketRepositoryInterface $ticketRepository
    ): void {
        try {
            // Recargar el ticket desde el repositorio
            $ticket = $ticketRepository->findById($this->ticketId);
            
            if (!$ticket) {
                throw new \Exception('Ticket no encontrado con ID: ' . $this->ticketId);
            }

            $this->safeLog('info', 'Generando recibo de entrada para ticket ID: ' . $this->ticketId);

            // Asegurar que el directorio existe
            $directory = 'receipts/entry';
            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }

            // Generar el PDF
            $pdf = $receiptService->generateEntryReceiptPdf($ticket);

            // Guardar el PDF en storage
            $filename = $directory . '/recibo-entrada-' . $ticket->getId() . '.pdf';
            Storage::disk('local')->put($filename, $pdf);

            $this->safeLog('info', 'Recibo de entrada generado exitosamente: ' . $filename);
        } catch (\Exception $e) {
            $this->safeLog('error', 'Error al generar recibo de entrada para ticket ID: ' . $this->ticketId . ' - ' . $e->getMessage());

            // Relanzar la excepción para que el job se reintente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->safeLog('error', 'Job de generación de recibo de entrada falló definitivamente - Ticket ID: ' . $this->ticketId . ' - Error: ' . $exception->getMessage());
    }

    /**
     * Log de forma segura sin fallar si no hay permisos
     */
    private function safeLog(string $level, string $message): void
    {
        try {
            switch ($level) {
                case 'info':
                    Log::info($message);
                    break;
                case 'error':
                    Log::error($message);
                    break;
                case 'warning':
                    Log::warning($message);
                    break;
                default:
                    Log::info($message);
            }
        } catch (\Exception $e) {
            // Silenciar errores de logging para evitar ciclos de errores
            // En producción, esto debería ser monitoreado de otra forma
        }
    }
}

