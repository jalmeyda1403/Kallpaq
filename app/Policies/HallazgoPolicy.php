<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hallazgo; // Import the Hallazgo model
use App\Models\Proceso; // Import the Proceso model
use Illuminate\Auth\Access\Response;

class HallazgoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all hallazgos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Other users can view hallazgos if they are associated with any OUO
        // that has any role in any process linked to any hallazgo.
        return $user->ouos()->whereHas('procesos', function ($q) {
            $q->whereHas('hallazgos', function ($subQ) {
                $subQ->where(function ($pivotQ) {
                    $pivotQ->wherePivot('responsable', true)
                           ->orWherePivot('delegada', true)
                           ->orWherePivot('sgc', true)
                           ->orWherePivot('sgas', true)
                           ->orWherePivot('sgcm', true)
                           ->orWherePivot('sgsi', true);
                });
            });
        })->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Hallazgo $hallazgo): bool
    {
        // Admins can view any hallazgo
        if ($user->hasRole('admin')) {
            return true;
        }

        // Other users can view a specific hallazgo if they are associated with an OUO
        // that has a relevant role for any process linked to this hallazgo.
        return $user->ouos()->whereHas('procesos', function ($q) use ($hallazgo) {
            $q->whereHas('hallazgos', function ($subQ) use ($hallazgo) {
                $subQ->where('hallazgos.id', $hallazgo->id);
            })->where(function ($pivotQ) {
                $pivotQ->wherePivot('responsable', true)
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
        // Admins can create hallazgos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Facilitators in any OUO linked to any process can create hallazgos
        return $user->ouoUsers()->where('role_in_ouo', 'facilitador')->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Hallazgo $hallazgo): bool
    {
        // Admins can update hallazgos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Facilitators in any OUO linked to a process associated with this hallazgo can update it
        return $user->ouoUsers()->where('role_in_ouo', 'facilitador')
                    ->whereHas('ouo', function ($q) use ($hallazgo) {
                        $q->whereHas('procesos', function ($subQ) use ($hallazgo) {
                            $subQ->whereHas('hallazgos', function ($pivotQ) use ($hallazgo) {
                                $pivotQ->where('hallazgos.id', $hallazgo->id);
                            });
                        });
                    })->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Hallazgo $hallazgo): bool
    {
        // Only admins can delete hallazgos
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Hallazgo $hallazgo): bool
    {
        // Only admins can restore hallazgos
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Hallazgo $hallazgo): bool
    {
        // Only admins can force delete hallazgos
        return $user->hasRole('admin');
    }
}
