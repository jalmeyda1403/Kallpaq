<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaAuditoria extends Model
{
    use HasFactory;

    protected $table = 'programa_auditoria';

    protected $fillable = [
        'pa_version',
        'pa_anio',
        'pa_recursos',
        'pa_fecha_aprobacion', // Maybe rename to pa_fecha_aprobacion in migration? Yes I did.
        'pa_estado',
        'pa_objetivo_general',
        'pa_alcance',
        'pa_metodos',
        'pa_criterios',
        'pa_riesgos',
        'pa_descripcion', // Renamed from observations
        'archivo_pdf',
        'fecha_publicacion',
        'avance'
    ];

    public function auditoriasEspecificas()
    {
        return $this->hasMany(AuditoriaEspecifica::class, 'pa_id');
    }

}