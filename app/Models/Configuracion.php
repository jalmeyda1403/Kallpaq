<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $fillable = [
        'id', 'clave', 'valor'];

    public static function getFechaInicioBloqueada()
    {
         $getfecha = Configuracion::where('clave', 'fecha_inicio_bloqueo')->value('valor');
         $dateArray = explode("/", $getfecha);
         $formattedDate = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
         $getfecha = Carbon::parse($formattedDate);
         return ($formattedDate);
    }

    public static function getFechaFinBloqueada()
    {
        $getfecha = Configuracion::where('clave', 'fecha_fin_bloqueo')->value('valor');
        $dateArray = explode("/", $getfecha);
         $formattedDate = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
         $getfecha = Carbon::parse($formattedDate);
         return ($formattedDate);
    }

    public function getPeriodoActual()
    {
        return Configuracion::where('clave', 'periodo_actual')->value('valor');
    }

    public function isPeriodoBloqueado($fecha)
    {
        $fecha = Carbon::parse($fecha);
        $fechaInicioBloqueo = Carbon::parse($this->getFechaInicioBloqueada());
        $fechaFinBloqueo = Carbon::parse($this->getFechaFinBloqueada());

        return $fecha->between($fechaInicioBloqueo, $fechaFinBloqueo, true);
    }
}

