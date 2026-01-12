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
        return view('obligaciones.index');
    }

    public function apiIndex(Request $request)
    {
        $query = Obligacion::with('proceso', 'area_compliance', 'documento', 'radar');

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

        // Filtrar por fuente
        if ($request->filled('fuente')) {
            $query->where('fuente', $request->fuente);
        }

        $obligaciones = $query->get();

        return response()->json($obligaciones);
    }

    public function misObligaciones(Request $request)
    {
        $user = auth()->user();

        // Obtener las OUOs del usuario
        $ouoIds = $user->ouos->pluck('id');

        // Obtener los procesos asociados a esas OUOs
        // Asumiendo que existe una relación 'procesos' en el modelo OUO
        // Si la relación es muchos a muchos, se accede a través de la tabla pivote
        $procesoIds = \App\Models\OUO::whereIn('id', $ouoIds)
            ->with('procesos')
            ->get()
            ->pluck('procesos')
            ->flatten()
            ->pluck('id')
            ->unique();

        $query = Obligacion::with('proceso', 'area_compliance', 'documento', 'radar')
            ->whereIn('proceso_id', $procesoIds);

        // Aplicar los mismos filtros que en apiIndex si es necesario
        if ($request->filled('proceso')) {
            $query->whereHas('proceso', function ($q) use ($request) {
                $q->where('proceso_nombre', 'like', '%' . $request->proceso . '%');
            });
        }

        if ($request->filled('documento')) {
            $query->where('documento_tecnico_normativo', 'like', '%' . $request->documento . '%');
        }

        if ($request->filled('fuente')) {
            $query->where('fuente', $request->fuente);
        }

        $obligaciones = $query->get();

        return response()->json($obligaciones);
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
                return response()->json(['message' => 'Obligación creada con éxito', 'obligacion' => $obligacion], 201);
            } else {
                // Si por alguna razón la creación falla, retornar un error
                return response()->json(['message' => 'Hubo un problema al crear la obligación. Inténtelo nuevamente.'], 500);
            }
        } catch (\Exception $e) {
            // Si ocurre una excepción, capturar el error y devolver un mensaje
            return response()->json(['message' => 'Error al crear la obligación: ' . $e->getMessage()], 500);
        }
    }



    public function show($id)
    {



        $obligacion = Obligacion::with('proceso', 'area_compliance')->findOrFail($id);



        return response()->json($obligacion);



    }







    public function update(Request $request, $id)
    {



        $obligacion = Obligacion::findOrFail($id);







        $obligacion->update($request->only([



            'proceso_id',



            'area_compliance_id',



            'documento_tecnico_normativo',



            'obligacion_principal',



            'obligacion_controles',



            'consecuencia_incumplimiento',



            'documento_deroga',



            'estado_obligacion',
            'radar_id',
            'documento_id',
            'tipo_obligacion',
            'nivel_riesgo_inherente',
            'nivel_riesgo_residual',
            'frecuencia_revision'
        ]));







        return response()->json(['message' => 'Obligación actualizada con éxito', 'obligacion' => $obligacion]);



    }







    public function destroy($id)
    {



        try {



            $obligacion = Obligacion::findOrFail($id);



            $obligacion->delete();



            return response()->json(['message' => 'Obligación eliminada correctamente.']);



        } catch (\Exception $e) {



            return response()->json(['message' => 'Ocurrió un error al eliminar la obligación.', 'error' => $e->getMessage()], 500);



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

    public function buscar(Request $request)
    {
        $q = $request->query('q', '');
        $obligaciones = Obligacion::where('obligacion_principal', 'LIKE', "%{$q}%")
                                  ->orWhere('documento_tecnico_normativo', 'LIKE', "%{$q}%")
                                  ->limit(20)
                                  ->get();

        $obligaciones = $obligaciones->map(function ($o) {
            $desc = $o->documento_tecnico_normativo ? "{$o->documento_tecnico_normativo} - " : "";
            $desc .= \Illuminate\Support\Str::limit($o->obligacion_principal, 50);
            return [
                'id' => $o->id,
                'descripcion' => $desc,
            ];
        });
        return response()->json($obligaciones);
    }
}
