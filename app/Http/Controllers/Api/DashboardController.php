<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Dashboard\GetDashboardStatsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(private GetDashboardStatsUseCase $getDashboardStatsUseCase) {}

    public function stats(Request $request): JsonResponse
    {
        try {
            return $this->sendResponse(
                $this->getDashboardStatsUseCase->execute(),
                'Dashboard statistics retrieved successfully'
            );
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
