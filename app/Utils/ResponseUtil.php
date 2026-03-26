<?php

namespace App\Utils;

class ResponseUtil
{
    /**
     * Build a standard success payload.
     */
    public static function makeResponse(string $message, mixed $data = null, array $meta = []): array
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (! is_null($data)) {
            $response['data'] = $data;
        }

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return $response;
    }

    /**
     * Build a standard error payload.
     */
    public static function makeError(string $message, array $errors = []): array
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
