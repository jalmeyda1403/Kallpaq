<?php

namespace App\Observers;

use App\Models\Accion;
use App\Models\Hallazgo;

class AccionObserver
{
    /**
     * Handle the Accion "created" event.
     */
    public function saved(Accion $accion)
    {
        // Encuentra el hallazgo asociado
        $hallazgo = Hallazgo::find($accion->hallazgo_id);

        if ($hallazgo) {
            // Obtener la última fecha de cierre de las acciones asociadas al hallazgo
            $ultimaFecha = Accion::where('hallazgo_id', $accion->hallazgo_id)
                                ->max('fecha_fin');

            // Actualizar la fecha de cierre de acciones en el hallazgo
            $hallazgo->fecha_cierre_acciones = $ultimaFecha;

            $totalAcciones = $hallazgo->acciones()->count();
            $accionesCompletadas = $hallazgo->acciones()->where('estado', 'Completada')->count();

            // Calcular el porcentaje de avance
            $avance = ($accionesCompletadas / $totalAcciones) * 100;

            // Actualizar el campo avance del hallazgo
            $hallazgo->avance = $avance;
            
            $hallazgo->save();
        }
    }

    /**
     * Handle the Accion "updated" event.
     */
    public function updated(Accion $accion): void
    {
        //
    }

    /**
     * Handle the Accion "deleted" event.
     */
    public function deleted(Accion $accion): void
    {
        //
    }

    /**
     * Handle the Accion "restored" event.
     */
    public function restored(Accion $accion): void
    {
        //
    }

    /**
     * Handle the Accion "force deleted" event.
     */
    public function forceDeleted(Accion $accion): void
    {
        //
    }
}
