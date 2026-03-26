<?php

namespace App\Listeners;

use App\Events\VehicleExitRegistered;
use App\Jobs\GenerateExitReceiptJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateExitReceiptListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VehicleExitRegistered $event): void
    {
        // Despachar el job para generar el recibo de forma asíncrona
        // Solo pasamos el ID del ticket para evitar problemas de serialización
        GenerateExitReceiptJob::dispatch($event->ticket->getId());
    }
}
