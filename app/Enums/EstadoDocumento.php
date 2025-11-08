<?php

namespace App\Enums;

enum EstadoDocumento: int
{
    case obsoleto = 0;
    case borrador = 1;
    case publicado = 2;
    case derogado = 3;



    public function nombre(): string
    {
        return match ($this) {
            self::obsoleto => 'Obsoleto',
            self::borrador => 'Borrador',
            self::publicado => 'Publicado',        
            self::derogado => 'Derogado',
        };
    }
}
