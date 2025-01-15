<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use PDOException;

class CheckDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        try {
            // Intentar realizar una consulta sencilla
            DB::connection()->getPdo();
        } catch (PDOException $e) {
            // Manejar la excepción cuando no se puede conectar a la base de datos
            if ($e->getCode() == 2002) {
                // Redirigir a una vista de error personalizada o mostrar un mensaje
                return response()->view('errors.database', [], 500);
            }
        }

        return $next($request);
    }
}
