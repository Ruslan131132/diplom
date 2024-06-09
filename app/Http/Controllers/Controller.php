<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public static function response($result, $message, $status = 200): JsonResponse
    {
        $response = [
            'data' => $result,
            'message' => __($message),
            'success' => true
        ];

        return response()->json($response, $status, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function error($error, $status): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => __($error),
            'status' => $status,
        ];

        return response()->json($response, $status, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
