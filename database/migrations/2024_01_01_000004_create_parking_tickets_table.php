<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkingTicketsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('restrict');
            $table->foreignId('parking_spot_id')->constrained('parking_spots')->onDelete('restrict');
            $table->foreignId('parking_lot_id')->constrained('parking_lots')->onDelete('restrict');
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->foreignId('entry_guard_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('exit_guard_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('hourly_rate_applied', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->enum('payment_method', ['cash', 'card', 'transfer'])->nullable();
            $table->dateTime('payment_time')->nullable();
            $table->timestamps();

            $table->index('vehicle_id');
            $table->index('parking_spot_id');
            $table->index('parking_lot_id');
            $table->index('entry_time');
            $table->index('exit_time');
            $table->index('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_tickets');
    }
}




