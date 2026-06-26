<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Throwable;

class LogHelper
{
    public static function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    public static function error(string $message, Throwable $exception, array $context = []): void
    {
        Log::error($message, array_merge($context, [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]));
    }
}
