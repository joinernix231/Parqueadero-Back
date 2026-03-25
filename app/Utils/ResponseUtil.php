<?php

namespace App\Utils;

class ResponseUtil
{
    /**
     * Construye una respuesta de éxito estándar.
     *
     * @param string $message
     * @param mixed  $data
     * @param array  $meta
     * @return array
     */
    public static function makeResponse(string $message, mixed $data = null, array $meta = []): array
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return $response;
    }

    /**
     * Construye una respuesta de error estándar.
     *
     * @param string $message
     * @param array  $errors
     * @return array
     */
    public static function makeError(string $message, array $errors = []): array
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}


