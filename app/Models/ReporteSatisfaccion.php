<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Proceso;
use App\Models\User;

class ReporteSatisfaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'anio',
        'trimestre',
        'fecha_generacion',
        'proceso_id',
        'resumen_encuestas',
        'resumen_sugerencias',
        'reclamos',
        'resumen_snc',
        'oportunidades_mejora',
        'conclusiones',
        'archivo_path',
        'estado',
        'user_id'
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
    ];

    protected $appends = ['archivo_path_url'];

    public function getArchivoPathUrlAttribute()
    {
        if ($this->archivo_path) {
            return Storage::url($this->archivo_path);
        }
        return null;
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
