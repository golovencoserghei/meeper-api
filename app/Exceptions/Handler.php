<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($e->getMessage() == '') {
            return response()->json(['status' => false, 'message' => "Route doesn't exist."], 404);
        }

        return response()->json(['status' => false, 'message' => $e->getMessage(), 'trace' => $e->getTrace()]);
    }
}
