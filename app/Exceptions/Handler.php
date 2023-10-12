<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (\Illuminate\Database\QueryException $e, Request $request) {
            if($e->getCode() == 23503 || $e->getCode() == 23000) {
                return response()->view('errors.foreign-key-violation', [], 500);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
