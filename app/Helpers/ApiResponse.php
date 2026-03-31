<?php
namespace App\Helpers;

class ApiResponse
{
    public static function success($message = 'Success', $data = null)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data
        ]);
    }

    public static function error($message = 'Error', $code = 400, $data = null)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}