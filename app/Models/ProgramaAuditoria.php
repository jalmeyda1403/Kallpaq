<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaAuditoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'avance',
        'periodo',
        'presupuesto',
        'fecha_aprobacion',
        'observaciones',
        'archivo_pdf',
    ];
}