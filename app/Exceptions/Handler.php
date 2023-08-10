<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // $this->reportable(function (Throwable $e) {
        //     //
        // });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/v1/vehicles/*')) { // <- Add your condition here
                return response()->json([
                    'message' => 'Vehicle record not found.'
                ], 404);
            }elseif ($request->is('api/v1/parkings/*')) { // Add condition for api/v1/parking
                return response()->json([
                    'message' => 'Parking record not found.'
                ], 404);
            }
        });
    }
}
