<?php

namespace App\Http\Traits;

trait ApiResponse
{
    /**
     * Return a success response
     */
    public function success($data = null, $message = 'Success', $code = 200, $meta = null)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
        
        if ($meta && is_array($meta)) {
            // If meta has a 'meta' key, use that; otherwise use the entire meta array
            $response['meta'] = isset($meta['meta']) ? $meta['meta'] : $meta;
        }
        
        return response()->json($response, $code);
    }

    /**
     * Return a created response
     */
    public function created($data = null, $message = 'Resource created successfully')
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    /**
     * Return an error response
     */
    public function error($message = 'Error', $code = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a not found response
     */
    public function notFound($message = 'Resource not found')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 404);
    }

    /**
     * Return an unauthorized response
     */
    public function unauthorized($message = 'Unauthorized')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], 401);
    }

    /**
     * Return a validation error response
     */
    public function validationError($errors, $message = 'Validation failed')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Return a paginated response
     */
    public function paginated($data, $message = 'Success', $code = 200, $meta = null)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ],
        ];

        if ($meta && is_array($meta)) {
            $response['meta'] = isset($meta['meta']) ? $meta['meta'] : $meta;
        }

        return response()->json($response, $code);
    }
}
