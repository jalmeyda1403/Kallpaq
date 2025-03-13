<?php

namespace App\Http\Controllers;
use App\Models\Riesgo;
use App\Models\Proceso;
use App\Models\Obligacion;
use Illuminate\Http\Request;
use App\Helpers\SemaforoHelper;

class RiesgoController extends Controller
{
    // Mostrar una lista de los riesgos
    public function index()
    {

        $riesgos = Riesgo::all();
        return view('riesgos.index', compact('riesgos'));
    }

    public function listar($proceso_id = null)
    {
        $proceso = Proceso::with('subprocesos.riesgos')->findOrFail($proceso_id);
        $riesgos = $proceso->riesgos->sortBy('id');
        // Función recursiva para obtener indicadores de subprocesos, nietos, etc.
        $riesgos = $this->obtenerRiesgosRecursivos($proceso, $riesgos);
        $riesgos = $riesgos->map(function ($riesgo) {
            $riesgo->semaforo = SemaforoHelper::getSemaforoColor($riesgo->riesgo_valoracion);
            return $riesgo;
        });
        return view('riesgos.index', compact('proceso', 'riesgos'));
    }

    private function obtenerRiesgosRecursivos($proceso, &$riesgos)
    {
        foreach ($proceso->subprocesos as $subproceso) {
            // Fusionar los indicadores del subproceso a la colección de indicadores
            $riesgos = $riesgos->merge($subproceso->riesgos);

            // Recursión para obtener indicadores de los subprocesos de los hijos (nietos, bisnietos...)
            $riesgos = $this->obtenerRiesgosRecursivos($subproceso, $riesgos);


        }
        return $riesgos;
    }


    public function store(Request $request)
    {
        // Crear el nuevo riesgo en la base de datos

        // Inicializar la variable de la obligación
        $obligacion = null;
        $proceso =null;
        $proceso_id = null;

        // Verificar si se recibe un `obligacion_id`
        if ($request->has('obligacion_id') && $request->obligacion_id) {
            // Obtener la obligación y su proceso_id
            $obligacion = Obligacion::find($request->obligacion_id);
            $proceso_id = $obligacion->proceso_id;
            $request->merge(['proceso_id' => $proceso_id]);
            }
            else{
                $proceso_id = $request->proceso_id;
            }
                // Crear el riesgo
        $riesgo = Riesgo::create($request->all());

        // Si hay una obligación asociada, asociamos el riesgo a la obligación
        if ($obligacion) {
            $obligacion->asociarRiesgo($riesgo);
            $riesgos = $obligacion->riesgos;
            
        }
        else  {
            $proceso = Proceso::find($request->proceso_id);
            $riesgos = $proceso->riesgos;
        }
        
         $riesgos->load('proceso');

        // Si no hay obligación, solo devolvemos el riesgo creado
        return response()->json($riesgos);

    }

    // Mostrar un riesgo específico
    public function show(Riesgo $riesgo)
    {

        $riesgo->load('proceso');
        return response()->json($riesgo);
    }

    // Mostrar el formulario para editar un riesgo existente
    public function edit(Riesgo $riesgo)
    {
        return view('riesgos.edit', compact('riesgo'));
    }

    // Actualizar un riesgo existente en la base de datos
    public function update(Request $request, Riesgo $riesgo)
    {
        $riesgo->update($request->all());
        $riesgo->load('proceso');

        return response()->json($riesgo);
    }

    // Eliminar un riesgo de la base de datos
    public function destroy(Riesgo $riesgo)
    {
        $riesgo->delete();

        return response()->json($riesgo);
    }
}