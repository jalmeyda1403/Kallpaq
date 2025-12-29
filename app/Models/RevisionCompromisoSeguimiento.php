<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisionCompromisoSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'revision_compromiso_seguimientos';

    protected $fillable = [
        'compromiso_id',
        'user_id',
        'comentario',
        'avance_anterior',
        'avance_nuevo',
        'estado_anterior',
        'estado_nuevo',
    ];

    /**
     * Compromiso al que pertenece
     */
    public function compromiso()
    {
        return $this->belongsTo(RevisionCompromiso::class, 'compromiso_id');
    }

    /**
     * Usuario que realizó el seguimiento
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Indica si hubo cambio de avance
     */
    public function getHuboCambioAvanceAttribute()
    {
        return $this->avance_anterior !== $this->avance_nuevo;
    }

    /**
     * Indica si hubo cambio de estado
     */
    public function getHuboCambioEstadoAttribute()
    {
        return $this->estado_anterior !== $this->estado_nuevo;
    }
}
