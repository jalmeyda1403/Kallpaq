<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Proceso; // Import the Proceso model
use Illuminate\Auth\Access\Response;

class ProcesoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all processes
        if ($user->hasRole('admin')) {
            return true;
        }

        // Other users can view processes if they are associated with any OUO
        // that has any role (responsable, delegada, sgc, etc.) in any process.
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
    public function view(User $user, Proceso $proceso): bool
    {
        // Admins can view any process
        if ($user->hasRole('admin')) {
            return true;
        }

        // Other users can view a specific process if they are associated with an OUO
        // that has a relevant role for this process.
        return $user->ouos()->whereHas('procesos', function ($q) use ($proceso) {
            $q->where('procesos.id', $proceso->id)
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
        // Only admins can create processes for now
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proceso $proceso): bool
    {
        // Only admins can update processes for now
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proceso $proceso): bool
    {
        // Only admins can delete processes for now
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proceso $proceso): bool
    {
        // Only admins can restore processes for now
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Proceso $proceso): bool
    {
        // Only admins can force delete processes for now
        return $user->hasRole('admin');
    }
}
