<?php

namespace App\Observers;
use App\Models\Hallazgo;
use App\Models\Accion;

class HallazgoObserver
{
    /**
     * Handle the Hallazgo "created" event.
     */
    public function created(Hallazgo $hallazgo): void
    {
        //
    }

    /**
     * Handle the Hallazgo "updated" event.
     */
    public function updated(Hallazgo $hallazgo)
    {
        if ($hallazgo->isDirty('estado') && $hallazgo->estado == 'Aprobado') {
            Accion::where('hallazgo_id', $hallazgo->id)->update(['estado' => 'Programada']);
        }
    }

    /**
     * Handle the Hallazgo "saved" event.
     * Automatically log state changes to hallazgo_movimientos.
     */
    public function saved(Hallazgo $hallazgo): void
    {
        // Verificar si el estado cambió
        if ($hallazgo->isDirty('hallazgo_estado')) {
            $comentario = "Estado cambiado a: {$hallazgo->hallazgo_estado}";

            // Personalizar el comentario según el estado
            if ($hallazgo->hallazgo_estado === 'evaluado') {
                $comentario = "Evaluación de eficacia sin eficacia. Se requiere plan de acción actualizado. Ciclo: {$hallazgo->hallazgo_ciclo}";
            } elseif ($hallazgo->hallazgo_estado === 'cerrado') {
                $comentario = "Evaluación de eficacia con eficacia. Hallazgo cerrado exitosamente.";
            } elseif ($hallazgo->hallazgo_estado === 'concluido') {
                $comentario = "Todas las acciones han sido implementadas o desestimadas. Pendiente verificación de eficacia.";
            }

            $hallazgo->movimientos()->create([
                'estado' => $hallazgo->hallazgo_estado,
                'comentario' => $comentario,
                'user_id' => \Auth::id() ?? 1, // Fallback a 1 si no hay usuario autenticado
            ]);
        }
    }

    /**
     * Handle the Hallazgo "deleted" event.
     */
    public function deleted(Hallazgo $hallazgo): void
    {
        //
    }

    /**
     * Handle the Hallazgo "restored" event.
     */
    public function restored(Hallazgo $hallazgo): void
    {
        //
    }

    /**
     * Handle the Hallazgo "force deleted" event.
     */
    public function forceDeleted(Hallazgo $hallazgo): void
    {
        //
    }
}
