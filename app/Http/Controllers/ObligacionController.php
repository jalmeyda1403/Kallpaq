<?php

namespace App\Http\Controllers;
use App\Helpers\SemaforoHelper;
use App\Models\Obligacion;
use App\Models\Riesgo;
use App\Models\AreaCompliance;
use App\Models\Proceso;
use Illuminate\Http\Request;

class ObligacionController extends Controller
{

    public function index(Request $request)
    {
        $query = Obligacion::with('proceso', 'area_compliance', 'riesgos');
        // Filtrar por nombre del proceso (relación)
        if ($request->filled('proceso')) {
            $query->whereHas('proceso', function ($q) use ($request) {
                $q->where('proceso_nombre', 'like', '%' . $request->proceso . '%');
            });
        }

        // Filtrar por nombre del documento
        if ($request->filled('documento')) {
            $query->where('documento_tecnico_normativo', 'like', '%' . $request->documento . '%');
        }

        $obligaciones = $query->get();

        return view('obligaciones.index', compact('obligaciones'));
    }

    public function listar($proceso_id = null)
    {
        $proceso = Proceso::with('subprocesos.obligaciones')->findOrFail($proceso_id);
        $obligaciones = $proceso->obligaciones->sortBy('id');
        // Función recursiva para obtener indicadores de subprocesos, nietos, etc.
        $obligaciones = $this->obtenerObligacionesRecursivos($proceso, $obligaciones);

        foreach ($obligaciones as $obligacion) {
            switch ($obligacion->estado_obligacion) {
                case 'pendiente':
                    $obligacion->estadoClass = 'bg-secondary';  // Gris claro
                    break;
                case 'mitigada':
                    $obligacion->estadoClass = 'bg-warning';  // Amarillo
                    break;
                case 'controlada':
                    $obligacion->estadoClass = 'bg-primary';  // Azul
                    break;
                case 'inactiva':
                case 'suspendida':
                    $obligacion->estadoClass = 'bg-dark';  // Gris oscuro
                    break;
                case 'vencida':
                    $obligacion->estadoClass = 'bg-danger';  // Rojo
                    break;
                default:
                    $obligacion->estadoClass = ''; // Si no hay estado, no asigna clase
            }
        }


        return view('obligaciones.index', compact('proceso', 'obligaciones'));
    }

    private function obtenerObligacionesRecursivos($proceso, &$obligaciones)
    {
        foreach ($proceso->subprocesos as $subproceso) {
            // Fusionar los indicadores del subproceso a la colección de indicadores
            $obligaciones = $obligaciones->merge($subproceso->obligaciones);

            // Recursión para obtener indicadores de los subprocesos de los hijos (nietos, bisnietos...)
            $obligaciones = $this->obtenerObligacionesRecursivos($subproceso, $obligaciones);


        }
        return $obligaciones;
    }



    public function create()
    {
        $procesos = Proceso::all();  // Obtener todos los procesos
        $areas_compliance = AreaCompliance::all();  // Obtener todas las áreas de compliance
        return view('obligaciones.create', compact('procesos', 'areas_compliance'));
    }


    public function store(Request $request)
    {

        try {
            // Intentar crear la obligación con los datos proporcionados
            $obligacion = Obligacion::create($request->all());

            // Verificar si la creación fue exitosa
            if ($obligacion) {
                // Redirigir a la lista de obligaciones con un mensaje de éxito
                return redirect()->back()->with('success', 'Obligación creada con éxito');
            } else {
                // Si por alguna razón la creación falla, retornar un error
                return redirect()->back()->with('error', 'Hubo un problema al crear la obligación. Inténtelo nuevamente.');
            }
        } catch (\Exception $e) {
            // Si ocurre una excepción, capturar el error y devolver un mensaje
            return redirect()->back()->with('error', 'Error al crear la obligación: ');
        }
    }



    public function show($id)
    {

        $obligacion = Obligacion::with('proceso', 'area_compliance')->findOrFail($id);

        return response()->json([
            'proceso_id' => $obligacion->proceso->id,
            'proceso_nombre' => $obligacion->proceso->proceso_nombre,
            'area_compliance_id' => $obligacion->area_compliance->id,
            'area_compliance_nombre' => $obligacion->area_compliance->area_compliance_nombre,
            'documento_tecnico_normativo' => $obligacion->documento_tecnico_normativo,
            'obligacion_principal' => $obligacion->obligacion_principal,
            'obligacion_controles' => $obligacion->obligacion_controles,
            'consecuencia_incumplimiento' => $obligacion->consecuencia_incumplimiento,
            'documento_deroga' => $obligacion->documento_deroga,
            'estado_obligacion' => $obligacion->estado_obligacion
        ]);
    }





    public function update(Request $request, $id)
    {
        $obligacion = Obligacion::findOrFail($id);


        // Actualizar la obligación con los nuevos datos
        $obligacion->update($request->only([
            'proceso_id',
            'area_compliance_id',
            'documento_tecnico_normativo',
            'obligacion_principal',
            'obligacion_controles',
            'consecuencia_incumplimiento',
            'documento_deroga',
            'estado_obligacion',
        ]));

        // Redirigir a la lista de obligaciones con un mensaje de éxito
        return redirect()->back()->with('success', 'Obligación actualizada con éxito');
    }


    public function destroy($id)
    {
        try {
            // Buscar la obligación por ID
            $obligacion = Obligacion::findOrFail($id);

            // Eliminar la obligación
            $obligacion->delete();

            return response()->json(['success' => 'Obligación eliminada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al eliminar la obligación.'], 500);
        }
    }

    public function listariesgos($id)
    {

        $obligacion = Obligacion::findOrFail($id);
        $riesgos = $obligacion->riesgos()->with('factor')->get();
        // Agregar la clase de color a cada riesgo
        $riesgos = $riesgos->map(function ($riesgo) {
            $riesgo->semaforo = SemaforoHelper::getSemaforoColor($riesgo->riesgo_valoracion);
            return $riesgo;
        });
        return response()->json($riesgos);
    }

}
