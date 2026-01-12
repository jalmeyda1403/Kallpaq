<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParteInteresada extends Model
{
    use HasFactory, SoftDeletes; // Usa el trait
    protected $table = 'partes_interesadas';  // Define el nombre de la tabla

    protected $fillable = [
        'id',
        'pi_nombre',
        'pi_tipo',
        'pi_nivel_influencia',
        'pi_nivel_interes',
        'pi_descripcion',
        'pi_cuadrante',
        'pi_valoracion',
        'pi_activo'
    ];
    protected $dates = ['deleted_at'];

    public function getValoracionAttribute()
    {
        $influencia = $this->getAttribute('pi_nivel_influencia');
        $interes = $this->getAttribute('pi_nivel_interes');

        // Definimos la matriz de evaluación
        $valoraciones = [
            'alto' => [
                'alto' => 'Clave / Asociarse',
                'medio' => 'Involucrar',
                'bajo' => 'Monitorear',
            ],
            'medio' => [
                'alto' => 'Clave / Asociarse',
                'medio' => 'Involucrar',
                'bajo' => 'Monitorear',
            ],
            'bajo' => [
                'alto' => 'Monitorear',
                'medio' => 'Mantener Informado',
                'bajo' => 'Mantener Informado',
            ],
        ];

        if (!$influencia || !$interes) return 'Evaluación Pendiente';

        return $valoraciones[$influencia][$interes] ?? 'Evaluación No Definida';
    }
    public function getCuadranteAttribute()
    {
        $influencia = $this->getAttribute('pi_nivel_influencia');
        $interes = $this->getAttribute('pi_nivel_interes');
        $cuadrantes = [
            'alto' => [
                'alto' => 'I',
                'medio' => 'II',
                'bajo' => 'III',
            ],
            'medio' => [
                'alto' => 'I',
                'medio' => 'II',
                'bajo' => 'III',
            ],
            'bajo' => [
                'alto' => 'III',
                'medio' => 'IV',
                'bajo' => 'IV',
            ],
        ];
        if (!$influencia || !$interes) return 'Pendiente';
        return $cuadrantes[$influencia][$interes] ?? 'Sin Clasificar';
    }

    public function getMensajeAttribute()
    {
        $cuadrante = $this->getCuadranteAttribute();


        switch ($cuadrante) {
            case 'I':
                return "La parte interesada se encuentra en el Cuadrante I. Esto indica que tiene **alto interés y alta influencia** sobre la organización. Se recomienda establecer una gestión activa, involucrándola en la toma de decisiones clave y manteniendo una comunicación frecuente.";
            case 'II':
                return "La parte interesada se encuentra en el Cuadrante II. Posee **alta influencia pero bajo o medio interés**. Es clave mantener informada a esta parte interesada y motivarla para que se involucre más en los objetivos institucionales.";
            case 'III':
                return "La parte interesada se encuentra en el Cuadrante III. Tiene **bajo o medio interés y baja o media influencia**. No requiere una gestión intensiva, pero sí un monitoreo ocasional y mantener abiertos los canales de información.";
            case 'IV':
                return "La parte interesada se encuentra en el Cuadrante IV. Tiene **alto interés pero baja o media influencia**. Es recomendable mantenerla bien informada, atender sus expectativas y motivarla, aunque no tenga poder de decisión.";
            default:
                return "La parte interesada aún no ha sido clasificada en un cuadrante. Es necesario completar la evaluación para determinar su estrategia de gestión.";
        }
    }

    protected static function booted()
    {
        static::saving(function ($parteInteresada) {
            $parteInteresada->pi_valoracion = $parteInteresada->valoracion;
            $parteInteresada->pi_cuadrante = $parteInteresada->cuadrante;
        });
        
        static::deleting(function ($parteInteresada) {
            // Si es soft delete y aún no está borrado
            if (!$parteInteresada->trashed()) {
                $parteInteresada->pi_activo = 0;
                $parteInteresada->save();
            }
        });

    }

    public function expectativas()
    {
        return $this->hasMany(Expectativa::class, 'parte_interesada_id');
    }
}
