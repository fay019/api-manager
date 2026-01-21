<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Log exceptions with additional context
     */
    public function report(Throwable $e): void
    {
        if ($this->shouldReport($e)) {
            Log::error('Exception occurred', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => request()->fullUrl() ?? 'N/A',
                'method' => request()->method() ?? 'N/A',
                'user_id' => auth()->check() ? auth()->id() : null,
                'ip' => request()->ip() ?? 'N/A',
            ]);
        }

        parent::report($e);
    }

    /**
     * Render exceptions
     */
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
