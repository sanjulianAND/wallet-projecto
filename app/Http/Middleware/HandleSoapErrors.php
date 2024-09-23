<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class HandleSoapErrors
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'cod_error' => 500,
                'message_error' => 'Ha ocurrido un error en el servidor.',
                'data' => [
                    'error_message' => $e->getMessage(),
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
