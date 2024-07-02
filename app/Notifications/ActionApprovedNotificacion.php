<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActionApprovedNotificacion extends Notification
{
    use Queueable;

    protected $accion;

    public function __construct($accion)
    {
        $this->accion = $accion;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Acción Aprobada')
                    ->greeting('Hola,'. $this->accion->responsable_id)
                    ->line('El presente es para comunicar que la acción con el código "' . $this->accion->accion_cod . '" ha sido aprobada.')
                    ->line('Descripción de la acción: "' . $this->accion->accion.'" cuya fecha de implementación vence el '. $this->accion->fecha_fin )
                    ->line('Gracias por su colaboración!');
    }

    public function toArray($notifiable)
    {
        return [
            'accion_id' => $this->accion->id,
            'responsable' => $this->accion->responsable_id,
            'codigo_accion' => $this->accion->accion_cod,
            'descripcion_accion' => $this->accion->accion,
        ];
    }
}
