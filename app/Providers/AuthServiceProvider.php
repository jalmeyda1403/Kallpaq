<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Proceso; // Import the Proceso model
use App\Policies\ProcesoPolicy; // Import the ProcesoPolicy
use App\Models\Hallazgo; // Import the Hallazgo model
use App\Policies\HallazgoPolicy; // Import the HallazgoPolicy
use App\Models\Requerimiento; // Import the Requerimiento model
use App\Policies\RequerimientoPolicy; // Import the RequerimientoPolicy

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Proceso::class => ProcesoPolicy::class,
        Hallazgo::class => HallazgoPolicy::class,
        Requerimiento::class => RequerimientoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
