<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Accion;
use App\Models\Hallazgo;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use App\Observers\AccionObserver;
use App\Observers\HallazgoObserver;
use App\Observers\DocumentoObserver;
use App\Observers\DocumentoVersionObserver;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Accion::observe(AccionObserver::class);
        Hallazgo::observe(HallazgoObserver::class);
        Documento::observe(DocumentoObserver::class);
        DocumentoVersion::observe(DocumentoVersionObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
