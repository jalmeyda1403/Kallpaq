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
