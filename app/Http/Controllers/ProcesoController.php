<?php

namespace App\Http\Controllers;
use App\Models\Inventario;
use Illuminate\Http\Request;
use App\Models\Proceso;
use App\Models\OUO;
use App\Models\Documento;
use App\Models\User;


class ProcesoController extends Controller
{
    public function index(Request $request)
    {


        $query = Proceso::query();
        $user = auth()->user(); // Get authenticated user

        // If the user is not an admin, filter processes based on their OUO roles
        if (!$user->hasRole('admin')) {
            $accessibleOuoIds = $user->ouos->pluck('id')->toArray();

            $query->whereHas('ouos', function ($q) use ($accessibleOuoIds) {
                $q->whereIn('ouos.id', $accessibleOuoIds)
                  ->where(function ($subQuery) {
                      $subQuery->wherePivot('propietario', true)
                               ->orWherePivot('delegado', true)
                               ->orWherePivot('ejecutor', true)
                               ->orWherePivot('sgc', true)
                               ->orWherePivot('sgas', true)
                               ->orWherePivot('sgcm', true)
                               ->orWherePivot('sgsi', true)
                               ->orWherePivot('sgco', true);
                  });
            });
        }

        // Filtrar si se selecciona un proceso padre
        if ($request->has('proceso_padre_id') && $request->proceso_padre_id != '') {
            $query->where('cod_proceso_padre', $request->proceso_padre_id);
        }

        // Buscar por nombre de proceso si se proporciona
        if ($request->has('buscar_proceso') && $request->buscar_proceso != '') {
            $query->where('proceso_nombre', 'LIKE', "%{$request->buscar_proceso}%");
        }

        $procesos = $query->get();
        // Filtrar procesos de nivel 0 como padres
        $procesos_padre = Proceso::where('proceso_nivel', 0)
            ->orderBy('cod_proceso')->get();

        return view('procesos.index', compact('procesos', 'procesos_padre'));
    }


    public function subprocesos($proceso_id)
    {

        $proceso = Proceso::findOrFail($proceso_id);
        $procesos = $proceso->subprocesos;

        // Filtrar procesos de nivel 0 como padres
        $procesos_padre = Proceso::where('proceso_nivel', 0)
            ->orderBy('cod_proceso')->get();

        return view('procesos.index', compact('procesos', 'procesos_padre'));
    }

    public function procesos_nivel($proceso_id)
    {


        $proceso = Proceso::findOrFail($proceso_id);
        $procesos = $proceso->subprocesos;

        // Filtrar procesos de nivel 0 como padres
        $proceso_padre = $proceso;

        if ($procesos->count() > 0) {
            return view('procesos.subprocesos', compact('procesos', 'proceso_padre'));
        } else {
            return redirect()->back()->with('error', 'No hay subprocesos para este proceso');
        }

    }

    public function create()
    {
        $procesos = Proceso::all();
        return view('procesos.create', compact('procesos'));
    }

    public function store(Request $request)
    {

        $proceso = Proceso::create($request->all());
        return redirect()->route('procesos.index')->with('success', 'Proceso creado correctamente');
    }

    public function edit(Proceso $proceso)
    {
        $procesosPadre = Proceso::where('cod_proceso_padre', '=', null)->get();
        return view('procesos.edit', compact('proceso', 'procesosPadre'));
    }

    public function show($proceso_id)
    {
        $proceso = Proceso::with('planificacion_pei')->findOrFail($proceso_id);


        return response()->json($proceso);
    }

    public function update(Request $request, $id)
    {
        $proceso = Proceso::findOrFail($id);

        $proceso->update($request->all());

        return response()->json(['success' => 'Proceso actualizado correctamente'], 200);

    }

    public function destroy($id)
    {
        $proceso = Proceso::findOrFail($id);

        $proceso->delete();
        return response()->json(['success' => true, 'message' => 'Proceso eliminado exitosamente.']);
    }

    public function findProcesos($proceso_id = null)
    {

        if ($proceso_id) {
            $proceso = Proceso::find($proceso_id);
            if ($proceso) {
                $procesos = $proceso->descendientes(); // Asegúrate de que descendientes() devuelva una colección
            } else {
                $procesos = collect(); // Devuelve una colección vacía si no se encuentra el proceso
            }
        } else {
            $procesos = Proceso::all();
        }

        // Usar map para transformar cada elemento de la colección
        $resultado = $procesos->map(function ($proceso) {
            return [
                'id' => $proceso->id,
                'descripcion' => "{$proceso->cod_proceso} - {$proceso->proceso_nombre}",
            ];
        });

        return response()->json($resultado);
    }
    public function buscar(Request $request)
    {
        $query = $request->query('q', '');
        $facilitadorId = $request->query('facilitador_id');

        $procesosQuery = Proceso::where('proceso_nombre', 'LIKE', "%{$query}%");

        if ($facilitadorId) {
            // Obtener los IDs de los procesos ya asociados a este facilitador
            $associatedProcesoIds = \DB::table('proceso_facilitador')
                                        ->where('facilitador_id', $facilitadorId)
                                        ->pluck('proceso_id');

            // Excluir estos procesos de la búsqueda
            $procesosQuery->whereNotIn('id', $associatedProcesoIds);
        }

        $procesos = $procesosQuery->get();

        $procesos = $procesos->map(function ($proceso) {
            return [
                'id' => $proceso->id,
                'descripcion' => $proceso->cod_proceso . ' - ' . $proceso->proceso_nombre,
            ];
        });

         return response()->json($procesos);
    }


    public function listar()
    {

        $procesos = Proceso::all();

        $formattedProcesos = $procesos->map(function ($proceso) {
            return [
                'id' => $proceso->id,
                'descripcion' => $proceso->proceso_nombre,
            ];
        });

        return response()->json($formattedProcesos);
    }
    public function mapaProcesos()
    {
        $inventarios = Inventario::all();
        $procesos = Proceso::whereNull('cod_proceso_padre')->orderBy('proceso_tipo')->get();
        return view('procesos.mapa', compact('inventarios', 'procesos'));

    }

    public function inventarioProcesos(Request $request)
    {
        $inventarios = Inventario::all();

        // Obtener el ID del inventario de la solicitud
        $inventarioId = $request->query('inventario_id');

        // Si se proporciona un ID de inventario, filtrar los procesos basados en este inventario
        if ($inventarioId) {
            // Primero verificar que el inventario exista
            $inventario = Inventario::find($inventarioId);
            if (!$inventario) {
                // Si no existe, usar el último inventario o mostrar todos
                $inventarioId = null;
            }
        }

        $procesos = Proceso::whereNull('cod_proceso_padre')->orderBy('proceso_tipo')->get();
        return view('procesos.inventario', compact('inventarios', 'procesos', 'inventarioId'));

    }

      public function apiMacroProcesos()
    {
        
            // Obtener procesos donde proceso_nivel = 0 (macroprocesos)
            // Asegúrate de que 'cod_proceso', 'proceso_nombre', 'proceso_padre_id', 'proceso_nivel' estén en $fillable del modelo Proceso
            $macroprocesos = Proceso::where('proceso_nivel', 0)
                                  ->select('id', 'cod_proceso', 'proceso_nombre', 'proceso_nivel') // Selecciona solo los campos necesarios
                                  ->orderBy('cod_proceso') // Ordena si es necesario
                                  ->get();

            return response()->json($macroprocesos);
      
    }
    //Asociar OUO
    public function listarOUO($proceso_id)
    {
        // Lógica para asociar procesos, por ejemplo:
        $proceso = Proceso::with('ouos')->findOrFail($proceso_id);

        $ouos = $proceso->ouos->map(function ($ouo) {
            // Accedemos a la data pivote directamente
            $ouo->propietario = (bool) $ouo->pivot->propietario;
            $ouo->delegado = (bool) $ouo->pivot->delegado;
            $ouo->ejecutor = (bool) $ouo->pivot->ejecutor;
            $ouo->sgc = (bool) $ouo->pivot->sgc;
            // ... repite para todos los demás flags de sistemas de gestión
            $ouo->sgas = (bool) $ouo->pivot->sgas;
            $ouo->sgcm = (bool) $ouo->pivot->sgcm;
            $ouo->sgsi = (bool) $ouo->pivot->sgsi;

            // Retornamos el objeto para mantener el resto de la data
            return $ouo;
        });

        return response()->json($ouos);
    }

    public function asociarOUO(Request $request, Proceso $proceso)
    {
        // Valida la solicitud para asegurar que el ouo_id está presente
        $request->validate([
            'ouo_id' => 'required|exists:ouos,id',
        ]);

        // Asocia la OUO al proceso con los datos de la tabla pivote
        $proceso->ouos()->attach($request->input('ouo_id'), [
            'propietario' => $request->boolean('propietario'),
            'delegado' => $request->boolean('delegado'),
            'ejecutor' => $request->boolean('ejecutor'),
            'sgc' => $request->boolean('sgc'),
            'sgas' => $request->boolean('sgas'),
            'sgcm' => $request->boolean('sgcm'),
            'sgsi' => $request->boolean('sgsi'),
            'sgco' => $request->boolean('sgco'),
        ]);

        return response()->json(['message' => 'Asociación creada correctamente.']);
    }
    public function updateOUO(Request $request, Proceso $proceso, Ouo $ouo)
    {
        // Valida la solicitud para asegurar que los campos son booleanos
        $request->validate([
            'propietario' => 'boolean',
            'delegado' => 'boolean',
            'ejecutor' => 'boolean',
            'sgc' => 'boolean',
            'sgas' => 'boolean',
            'sgcm' => 'boolean',
            'sgsi' => 'boolean',
            'sgco' => 'boolean',           
        ]);

        // Actualiza la fila existente en la tabla pivote
        $proceso->ouos()->updateExistingPivot($ouo->id, [
            'propietario' => $request->boolean('propietario'),
            'delegado' => $request->boolean('delegado'),
            'ejecutor' => $request->boolean('ejecutor'),
            'sgc' => $request->boolean('sgc'),
            'sgas' => $request->boolean('sgas'),
            'sgcm' => $request->boolean('sgcm'),
            'sgsi' => $request->boolean('sgsi'),
            'sgco' => $request->boolean('sgco'),
        ]);

        return response()->json(['message' => 'Flags actualizados correctamente.']);
    }
    public function disociarOUO(Proceso $proceso, Ouo $ouo)
    {
        // Usa el método detach() para eliminar el registro de la tabla pivote
        $proceso->ouos()->detach($ouo->id);

        return response()->json(['message' => 'Asociación eliminada correctamente.']);
    }
    //Asociar Documentos
    public function listarDocumentos($proceso_id)
    {
        $proceso = Proceso::with('documentos')->findOrFail($proceso_id);
        // Carga la relación 'documentos' de forma anticipada y retorna la colección
        return response()->json($proceso->documentos()->with('ultimaVersion')->get());
    }

    public function asociarDocumentos(Request $request, Proceso $proceso)
    {
        $request->validate(['documento_id' => 'required|exists:documentos,id']);

        $proceso->documentos()->attach($request->input('documento_id'));

        return response()->json(['message' => 'Documento asociado correctamente.']);
    }
    public function updateDocumentos(Request $request, Proceso $proceso, Ouo $ouo)
    {
        // Valida la solicitud para asegurar que los campos son booleanos
        $request->validate([
             'propietario' => 'boolean',
            'delegado' => 'boolean',
            'ejecutor' => 'boolean',
            'sgc' => 'boolean',
            'sgas' => 'boolean',
            'sgcm' => 'boolean',
            'sgsi' => 'boolean',
            'sgco' => 'boolean',   
        ]);

        // Actualiza la fila existente en la tabla pivote
        $proceso->ouos()->updateExistingPivot($ouo->id, [
             'propietario' => $request->boolean('propietario'),
            'delegado' => $request->boolean('delegado'),
            'ejecutor' => $request->boolean('ejecutor'),
            'sgc' => $request->boolean('sgc'),
            'sgas' => $request->boolean('sgas'),
            'sgcm' => $request->boolean('sgcm'),
            'sgsi' => $request->boolean('sgsi'),
            'sgco' => $request->boolean('sgco'),
        ]);

        return response()->json(['message' => 'Flags actualizados correctamente.']);
    }
    public function disociarDocumentos(Proceso $proceso, Documento $documento)
    {
        // Usa el método detach() para eliminar el registro de la tabla pivote
        $proceso->documentos()->detach($documento->id);

        return response()->json(['message' => 'Asociación eliminada correctamente.']);
    }

}