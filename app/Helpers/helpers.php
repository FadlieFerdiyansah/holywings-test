<?php

if (! function_exists('api_response')) {
    function api_response($message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
