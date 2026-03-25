<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ParkingEntryController;
use App\Http\Controllers\Api\ParkingExitController;
use App\Http\Controllers\Api\ParkingPaymentController;
use App\Http\Controllers\Api\ParkingQueryController;
use App\Http\Controllers\Api\ParkingReceiptController;
use App\Http\Controllers\Api\ParkingLotController;
use App\Http\Controllers\Api\ParkingSpotController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Vehicles
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::get('/vehicles/search-by-plate', [VehicleController::class, 'searchByPlate']);
    Route::get('/vehicles/{id}', [VehicleController::class, 'show']);

    // Parking lots
    Route::get('/parking-lots', [ParkingLotController::class, 'index']);
    Route::post('/parking-lots', [ParkingLotController::class, 'store']);
    Route::get('/parking-lots/{id}', [ParkingLotController::class, 'show']);
    Route::put('/parking-lots/{id}', [ParkingLotController::class, 'update']);

    // Parking spots
    Route::get('/parking-spots/available', [ParkingSpotController::class, 'available']);

    // Parking operations
    Route::post('/parking/entry', [ParkingEntryController::class, 'store']);
    Route::post('/parking/exit', [ParkingExitController::class, 'store']);
    Route::post('/parking/payment', [ParkingPaymentController::class, 'process']);

    // Ticket queries
    Route::get('/parking/tickets/{id}', [ParkingQueryController::class, 'show']);
    Route::get('/parking/tickets/{id}/calculate-price', [ParkingPaymentController::class, 'calculatePrice']);
    Route::get('/parking/tickets/by-plate/{plate}', [ParkingQueryController::class, 'findByPlate']);
    Route::get('/parking/current', [ParkingQueryController::class, 'current']);
    Route::get('/parking/history', [ParkingQueryController::class, 'history']);

    // Receipts
    Route::get('/parking/tickets/{id}/receipt/entry', [ParkingReceiptController::class, 'downloadEntry']);
    Route::get('/parking/tickets/{id}/receipt/exit', [ParkingReceiptController::class, 'downloadExit']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
});
