<?php

namespace App\Helpers;

class SemaforoHelper {
    public static function getSemaforoColor($valoracion) {
        return match(strtolower($valoracion)) {
            'bajo' => 'success', // Verde
            'medio' => 'warning', // Amarillo
            'alto' => 'orange', // Naranja
            'muy alto' => 'danger', // Rojo
            default => 'secondary', // Gris
        };
    }
}
