<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    /**
     * Standard success JSON response.
     */
    protected function sendResponse(mixed $result = null, string $message = 'Operation successful', array $meta = []): JsonResponse
    {
        return response()->json(
            ResponseUtil::makeResponse($message, $result, $meta)
        );
    }

    /**
     * Standard error JSON response.
     */
    protected function sendError(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        return response()->json(
            ResponseUtil::makeError($message, $errors),
            $code
        );
    }
}
