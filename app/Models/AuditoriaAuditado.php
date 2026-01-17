<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaAuditado extends Model
{
    use HasFactory;

    protected $table = 'auditoria_auditados';

    protected $fillable = [
        'agenda_id',
        'nombre',
        'cargo',
        'correo'
    ];

    public function agenda()
    {
        return $this->belongsTo(AuditoriaAgenda::class, 'agenda_id');
    }
}
