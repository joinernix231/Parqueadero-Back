<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParkingController;
use App\Http\Controllers\Api\ParkingLotController;
use App\Http\Controllers\Api\ParkingSpotController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);

    // Vehículos
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::get('/vehicles/search-by-plate', [VehicleController::class, 'searchByPlate']);
    Route::get('/vehicles/{id}', [VehicleController::class, 'show']);

    // Estacionamientos
    Route::get('/parking-lots', [ParkingLotController::class, 'index']);
    Route::get('/parking-lots/{id}', [ParkingLotController::class, 'show']);

    // Espacios
    Route::get('/parking-spots/available', [ParkingSpotController::class, 'available']);

    // Parqueadero
    Route::post('/parking/entry', [ParkingController::class, 'storeEntry']);
    Route::post('/parking/exit', [ParkingController::class, 'storeExit']);
    Route::post('/parking/payment', [ParkingController::class, 'processPayment']);
    Route::get('/parking/tickets/{id}', [ParkingController::class, 'show']);
    Route::get('/parking/tickets/{id}/calculate-price', [ParkingController::class, 'calculatePrice']);
    Route::get('/parking/tickets/{id}/receipt/entry', [ParkingController::class, 'downloadEntryReceipt']);
    Route::get('/parking/tickets/{id}/receipt/exit', [ParkingController::class, 'downloadExitReceipt']);
    Route::get('/parking/tickets/by-plate/{plate}', [ParkingController::class, 'findByPlate']);
    Route::get('/parking/current', [ParkingController::class, 'current']);
    Route::get('/parking/history', [ParkingController::class, 'history']);

    // Usuario autenticado
    Route::get('/user', function (Request $request) {
        $userEntity = app(\App\Domain\Repositories\UserRepositoryInterface::class)
            ->findById($request->user()->id);
        return new \App\Http\Resources\UserResource($userEntity);
    });
});
