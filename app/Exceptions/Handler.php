<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;


use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }



    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => __('Los datos proporcionados no son válidos.'),
            'errors' => $exception->errors(),
            // 'mensaje' => __('Los datos proporcionados no son válidos.'),
            // 'errores' => $exception->errors(),
        ], $exception->status);
    }
    // Método para retornar excepciones JSON
    public function render($request, Throwable $exception)
    {
        if($exception instanceof ModelNotFoundException){
            return response()->json(["message" => false, "error" => "Error modelo no encontrado"], 400);
            // return response()->json(["res" => false, "error" => "Error dato no encontrado"], 400);
        }
        // Capturar exceltion de Sanctum
        if($exception instanceof RouteNotFoundException){
            return response()->json(["message" => false, "error" => "No tiene permiso para acceder a esta ruta"], 401);
            // return response()->json(["res" => false, "error" => "No tiene permiso para acceder a esta ruta"], 401);
        }
        return parent::render($request, $exception);
    }



}
