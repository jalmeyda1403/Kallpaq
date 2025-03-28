<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Accion;
use App\Observers\AccionObserver;
use App\Models\Hallazgo;
use App\Observers\HallazgoObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Accion::observe(AccionObserver::class);
        Hallazgo::observe(HallazgoObserver::class);

    }
}
