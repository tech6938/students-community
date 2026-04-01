<?php
namespace App\Helpers;

class ApiResponse
{
    public static function success($message, $data = null, $code = 200)
    {
        $response = [
            'success' => true,
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
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($error)) {
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }
}   
