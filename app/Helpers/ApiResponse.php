<?php
namespace App\Helpers;

class ApiResponse
{
    public static function success($message, $data = null, $code = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function error($message, $code = 500, $error = null)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (!is_null($error)) {
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }

    public static function badRequest($message, $code = 400, $error = null)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (!is_null($error)) {
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }
}
