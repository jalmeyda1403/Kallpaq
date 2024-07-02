<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccionAlertaNotificacion extends Notification
{
    use Queueable;

    protected $accion;

    public function __construct($accion)
    {
        $this->accion = $accion;
    }

    public function via($notifiable)
    {
        return ['mail']; // Puedes agregar otros canales como 'database'
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Acción proxima a vencer')
            ->greeting('Hola, ' . $this->accion->responsable_id)
            ->line('La acción ' . $this->accion->accion_cod . ' está próxima a su fecha de finalización:' . $this->accion->fecha_fin)
            ->line('Agradeceré, tome las medidas necesarias.');
    }

    public function toArray($notifiable)
    {
        return [
            'accion_id' => $this->accion->id,
            'mensaje' => 'La acción ' . $this->accion->nombre . ' está próxima a su fecha de finalización.',
        ];
    }
}