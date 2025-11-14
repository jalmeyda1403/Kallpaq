<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Proceso extends Model
{
    use HasFactory;    protected $table = 'procesos';
    protected $cachedDescDocs = null;

    protected $fillable = [
        'id',
        'planificacion_pei_id',
        'cod_proceso',
        'proceso_sigla',
        'proceso_nombre',
        'proceso_objetivo',
        'proceso_tipo',
        'cod_proceso_padre',
        'proceso_nivel',
        'proceso_estado',
        'sgc',
        'sgas',
        'sgcm',
        'sgsi',
        'sgco',        
        'inactivated_at'
    ];

    public function procesoPadre()
    {
        return $this->belongsTo(Proceso::class, 'cod_proceso_padre');
    }
    public function procesoPadreNivel($nivel)
    {
        $proceso = $this;
        while ($proceso->proceso_nivel > $nivel && $proceso->procesoPadre) {
            $proceso = $proceso->procesoPadre;
        }
        return $proceso->proceso_nivel == $nivel ? $proceso : null;
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'proceso_id', 'id');
    }
    public function subprocesos()
    {
        return $this->hasMany(Proceso::class, 'cod_proceso_padre');
    }
    public function hallazgos()
    {
        return $this->belongsToMany(Hallazgo::class, 'hallazgo_proceso', 'proceso_id', 'hallazgo_id')
                    ->using(HallazgoProceso::class);
    }

    public function hallazgoProcesos()
    {
        return $this->hasMany(HallazgoProceso::class);
    }

    public function ouos()
    {
        return $this->belongsToMany(Ouo::class, 'procesos_ouo', 'id_proceso', 'id_ouo')
            ->using(ProcesoOuo::class) // Use the pivot model
            ->withPivot('propietario', 'delegado','ejecutor','sgc', 'sgas', 'sgcm', 'sgsi', 'sgco');
    }
    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class, 'proceso_id', 'id');
    }

    public function riesgos()
    {
        return $this->hasMany(Riesgo::class, 'proceso_id', 'id');
    }
    public function planificacion_pei()
    {
        return $this->belongsTo(PlanificacionPEI::class, 'planificacion_pei_id');
    }
    public function sipoc()
    {
        return $this->hasMany(Sipoc::class);
    }
    public function salidas()
    {
        return $this->hasMany(Salida::class);  // Relación de un subproceso a muchas salidas
    }
    public function documentos()
    {
        return $this->belongsToMany(Documento::class, 'documento_proceso', 'proceso_id', 'documento_id');
    }

    // Removed facilitadores() and users() relationships as per new architecture

    public function ouo_responsable()
    {
        return $this->ouos()->wherePivot('propietario', 1)->pluck('ouo_nombre')->first();
    }
    public function descendientes()
    {
        $descendientes = $this->subprocesos;

        foreach ($this->subprocesos as $subproceso) {
            $descendientes = $descendientes->merge($subproceso->descendientes());
        }

        return $descendientes;
    }
    public function descendiente_salidas()
    {
        $procesos = $this->descendientes()->push($this);

        return Salida::whereIn('proceso_id', $procesos->pluck('id'))->get();
    }
    public function descendiente_documentos()
    {
        if ($this->cachedDescDocs) {
            return $this->cachedDescDocs;
        }
        // Obtener todos los procesos descendientes y agregar el proceso actual
        $procesosIds = $this->descendientes()->pluck('id')->push($this->id);

        // Buscar los documentos cuya relación proceso_id esté en los procesos obtenidos
        return $this->cachedDescDocs = Documento::whereHas('procesos', function ($query) use ($procesosIds) {
            $query->whereIn('procesos.id', $procesosIds);
        })->get();
    }


}