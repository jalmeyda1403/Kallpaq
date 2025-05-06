<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class ActionApproved extends Notification
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
                        ->greeting('Hola,')
                        ->line('La acción con el código "' . $this->accion->codigo . '" ha sido aprobada.')
                        ->line('Descripción de la acción: ' . $this->accion->descripcion)
                        ->line('Gracias por tu colaboración!');
        }
    
        public function toArray($notifiable)
        {
            return [
                'accion_id' => $this->accion->id,
                'codigo_accion' => $this->accion->codigo,
                'descripcion_accion' => $this->accion->descripcion,
            ];
        }
    }