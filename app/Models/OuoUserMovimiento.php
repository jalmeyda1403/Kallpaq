<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OuoUserMovimiento extends Model
{
    use HasFactory;

    protected $table = 'ouo_user_movimientos';

    protected $fillable = [
        'ouo_user_id',
        'cambiado_por',
        'tipo_anterior',
        'tipo_nuevo',
        'motivo',
        'fecha_cambio',
    ];

    public $timestamps = false; // No se necesitan created_at y updated_at

    /**
     * Get the ouoUser that owns the OuoUserMovimiento.
     */
    public function ouoUser()
    {
        return $this->belongsTo(OuoUser::class);
    }

    /**
     * Get the user who made the change.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'cambiado_por');
    }
}
