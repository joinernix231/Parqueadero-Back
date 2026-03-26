<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->string('owner_name');
            $table->string('phone');
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck'])->default('car');
            $table->timestamps();

            $table->index('plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
}
