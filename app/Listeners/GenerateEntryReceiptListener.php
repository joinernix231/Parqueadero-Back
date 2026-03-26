<?php

namespace App\Listeners;

use App\Events\VehicleEntryRegistered;
use App\Jobs\GenerateEntryReceiptJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateEntryReceiptListener implements ShouldQueue
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
    public function handle(VehicleEntryRegistered $event): void
    {
        // Despachar el job para generar el recibo de forma asíncrona
        // Solo pasamos el ID del ticket para evitar problemas de serialización
        GenerateEntryReceiptJob::dispatch($event->ticket->getId());
    }
}
