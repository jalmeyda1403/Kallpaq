<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Requerimiento; // Import the Requerimiento model
use App\Models\Proceso; // Import the Proceso model
use App\Models\OUO; // Import the OUO model
use Illuminate\Auth\Access\Response;

class RequerimientoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all requerimientos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Other users can view requerimientos if they are associated with any OUO
        // that has any role in any process.
        return $user->ouos()->whereHas('procesos', function ($q) {
            $q->where(function ($subQuery) {
                $subQuery->wherePivot('responsable', true)
                         ->orWherePivot('delegada', true)
                         ->orWherePivot('sgc', true)
                         ->orWherePivot('sgas', true)
                         ->orWherePivot('sgcm', true)
                         ->orWherePivot('sgsi', true);
            });
        })->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Requerimiento $requerimiento): bool
    {
        // Admins can view any requerimiento
        if ($user->hasRole('admin')) {
            return true;
        }

        // Other users can view a specific requerimiento if they are associated with an OUO
        // that has a relevant role for the process linked to this requerimiento.
        return $user->ouos()->whereHas('procesos', function ($q) use ($requerimiento) {
            $q->where('procesos.id', $requerimiento->proceso_id)
              ->where(function ($subQuery) {
                  $subQuery->wherePivot('responsable', true)
                           ->orWherePivot('delegada', true)
                           ->orWherePivot('sgc', true)
                           ->orWherePivot('sgas', true)
                           ->orWherePivot('sgcm', true)
                           ->orWherePivot('sgsi', true);
              });
        })->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins can create requerimientos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Facilitators in any OUO can create requerimientos
        return $user->ouos()->wherePivot('role_in_ouo', 'facilitador')->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Requerimiento $requerimiento): bool
    {
        // Admins can update requerimientos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Facilitators in an OUO linked to the requerimiento's process can update it
        return $user->ouos()->wherePivot('role_in_ouo', 'facilitador')
                    ->whereHas('procesos', function ($q) use ($requerimiento) {
                        $q->where('procesos.id', $requerimiento->proceso_id);
                    })->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Requerimiento $requerimiento): bool
    {
        // Only admins can delete requerimientos
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Requerimiento $requerimiento): bool
    {
        // Only admins can restore requerimientos
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Requerimiento $requerimiento): bool
    {
        // Only admins can force delete requerimientos
        return $user->hasRole('admin');
    }
}
