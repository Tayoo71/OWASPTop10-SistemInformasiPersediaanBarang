<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Exception;

abstract class Controller
{
    // Function Handling and Logging Error
    protected function handleException(Exception $e, $request, $customMessage, $redirect)
    {
        Log::error('Error in ' . get_class($this) . ': ' . $e->getMessage(), [
            'request_data' => $request,
            'exception_trace' => $e->getTraceAsString(),
        ]);
        return redirect()->route($redirect)->withErrors($customMessage);
    }
    protected function logAPIValidationErrors($validator, $request)
    {
        // Log validation errors
        Log::error('Validation failed in' . get_class($this) .  'APIController', [
            'request_data' => $request->all(),
            'validation_errors' => $validator->errors(),
        ]);

        // Abort and return a 404 page
        abort(404);
    }
}
