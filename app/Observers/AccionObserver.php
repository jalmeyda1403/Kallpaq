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
        $hallazgo = Hallazgo::find($accion->accion_hallazgo_id);

        if ($hallazgo) {
            // Obtener la última fecha de cierre de las acciones asociadas al hallazgo
            $ultimaFecha = Accion::where('accion_hallazgo_id', $accion->accion_hallazgo_id)
                ->max('accion_fecha_fin_planificada');

            // Actualizar la fecha de cierre de acciones en el hallazgo
            $hallazgo->hallazgo_fecha_cierre = $ultimaFecha;

            $totalAcciones = $hallazgo->acciones()->count();

            // Contar acciones completadas (implementada o desestimada)
            $accionesCompletadas = $hallazgo->acciones()
                ->whereIn('accion_estado', ['implementada', 'desestimada'])
                ->count();

            // Calcular el porcentaje de avance
            $avance = ($totalAcciones > 0) ? ($accionesCompletadas / $totalAcciones) * 100 : 0;

            // Actualizar el campo avance del hallazgo
            $hallazgo->hallazgo_avance = $avance;

            // Verificar si todas las acciones están concluidas para cambiar el estado del hallazgo
            if ($totalAcciones > 0 && $accionesCompletadas === $totalAcciones && $hallazgo->hallazgo_estado !== 'concluido') {
                $hallazgo->hallazgo_estado = 'concluido';
                $hallazgo->hallazgo_fecha_conclusion = now();

                // Registrar el movimiento
                $hallazgo->movimientos()->create([
                    'hm_estado' => 'concluido',
                    'hm_comentario' => 'Hallazgo concluido automáticamente al completarse todas las acciones.',
                    'user_id' => auth()->id() ?? 1, // Usar el usuario actual o un ID por defecto si es una tarea programada
                ]);
            }

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
