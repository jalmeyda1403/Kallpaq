<?php

namespace App\Helpers;

class SemaforoHelper
{
    public static function getSemaforoColor($valoracion)
    {
        $valoracion = strtolower(trim($valoracion));

        // Mapeo femenino → masculino
        $equivalencias = [
            'baja' => 'bajo',
            'media' => 'medio',
            'alta' => 'alto',
            'muy alta' => 'muy alto',
        ];

        // Reemplaza si está en las equivalencias
        if (array_key_exists($valoracion, $equivalencias)) {
            $valoracion = $equivalencias[$valoracion];
        }

        return match ($valoracion) {
            'bajo' => 'success',     // Verde
            'medio' => 'warning',    // Amarillo
            'alto' => 'orange',      // Naranja
            'muy alto' => 'danger',  // Rojo
            default => 'secondary',  // Gris
        };
    }
}
