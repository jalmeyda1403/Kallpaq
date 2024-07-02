<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Notification;
use App\Models\Accion;
use App\Notifications\AccionAlertaNotificacion;
use Carbon\Carbon;

class NotificarAccionesProximas extends Command
{
    protected $signature = 'notificar:acciones-proximas';
    protected $description = 'Notificar a los responsables de acciones próximas a su fecha de finalización';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $fechaActual = Carbon::now();
        // Actualizar las acciones al estado de implementación que han alcanzado la fecha de inicio
        Accion::where('estado', ['Programada'])
        ->whereDate('fecha_inicio', '<=', $fechaActual)
        ->update(['estado' => 'En implementación']);
        
        // Actualizar las acciones con estado Pendiente que han pasado su fecha de fin
        Accion::whereIn('estado', ['Programada', 'En implementacion'])
            ->whereDate('fecha_fin', '<', $fechaActual)
            ->update(['estado' => 'Pendiente']);       

        $fechaLimite = Carbon::now()->addDays(3);
        $acciones = Accion::whereNotIn('estado', ['Completada', 'Cancelada', 'Cerrada', 'Programada'])
            ->whereDate('fecha_fin', '<=', $fechaLimite)
            ->get(); 
        $accionesChunks = $acciones->chunk(4);              
        foreach ($accionesChunks as $chunk) {
            foreach ($chunk as $accion) {
                $responsableCorreo = $accion->responsable_correo;              
                 Notification::route('mail', $responsableCorreo)->notify(new AccionAlertaNotificacion($accion));
                }
            
            sleep(30);
        }
         $this->info('Notificaciones enviadas a los responsables de acciones próximas a su fecha de finalización.');
    }
}