<?php

namespace App\Enums;

use function Laravel\Prompts\text;

enum SistemasGestion: text
{
    case sgc = '9001';
    case sgas = '37001';
    case sgcm = '37301';
    
    case sgsi = '27001';

    case sgco = '21001';


    public function nombre(): string
    {
        return match ($this) {
            self::sgc => '9001',
            self::sgas => '37001',
            self::sgcm => '37301',        
            self::sgsi => '27001',
            self::sgco => '21001',
        };
    }
}
