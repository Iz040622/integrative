<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their custom log levels.
     */
    protected $levels = [];

    /**
     * A list of exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
