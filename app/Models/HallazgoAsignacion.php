<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallazgoAsignacion extends Model
{
      protected $table = 'hallazgo_asignaciones';
  protected $fillable = [
        'hallazgo_id',
        'especialista_id',
        'user_asigna_id',
    ];

    /**
     * Una asignación pertenece a un hallazgo.
     */
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

    /**
     * El especialista que fue asignado.
     */
    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }

    /**
     * El usuario que realizó la asignación.
     */
    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'user_asigna_id');
    }
}

