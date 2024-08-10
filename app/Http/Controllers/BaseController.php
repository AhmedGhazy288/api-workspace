<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{

    /**
     * success response method.
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $message = null, $code = 200): JsonResponse
    {
        $response['success'] = true;

        if (!is_null($message))
            $response['message'] = $message;

        $response['data'] = $result;

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}

