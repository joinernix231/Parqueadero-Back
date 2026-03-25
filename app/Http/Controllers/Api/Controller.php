<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Utils\ResponseUtil;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    /**
     * Respuesta de éxito estándar.
     */
    protected function sendResponse(mixed $result = null, string $message = 'Operación exitosa', array $meta = []): JsonResponse
    {
        return response()->json(
            ResponseUtil::makeResponse($message, $result, $meta)
        );
    }

    /**
     * Respuesta de error estándar.
     */
    protected function sendError(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        return response()->json(
            ResponseUtil::makeError($message, $errors),
            $code
        );
    }
}





