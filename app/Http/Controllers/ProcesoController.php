<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Proceso;
use App\Models\OUO;
use App\Models\User;

class ProcesoController extends Controller
{
    public function index(Request $request)
    {

         $query = Proceso::query();

        // Filtrar si se selecciona un proceso padre
        if ($request->has('proceso_padre_id') && $request->proceso_padre_id != '') {
            $query->where('cod_proceso_padre', $request->proceso_padre_id);
        } else {
            // Filtrar procesos de nivel 0 o nivel 1
            $query->whereIn('proceso_nivel', [0, 1]);
        }
    
        $procesos = $query->get();
        $procesos_padre = Proceso::where('proceso_nivel', 0)->get(); // Solo procesos de nivel 0 como padres
    
        return view('procesos.index', compact('procesos', 'procesos_padre'));
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

    public function update(Request $request, Proceso $proceso)
    {
        // return dd ($request);
        $proceso->update($request->all());

        return redirect()->route('procesos.index')->with('success', 'Proceso actualizado correctamente');
    }

    public function destroy(Proceso $proceso)
    {
        $proceso->delete();
        return redirect()->route('procesos.index')->with('success', 'Proceso eliminado correctamente');
    }

    public function findProcesos($proceso_id = null)
    {
        $resultado = [];

        if ($proceso_id) {
            // Si se pasa un proceso_id, obtener ese proceso específico con sus hijos y nietos
            $procesos = Proceso::select('id', 'cod_proceso', 'proceso_nombre')
                ->with('subprocesos.subprocesos')
                ->where('id', $proceso_id)
                ->first();

            if ($procesos) {
                $resultado = $this->getFlatProcessList($procesos);
            }
        } else {
            // Si no se pasa un proceso_id, devolver todos los procesos
            $procesos = Proceso::select('id', 'cod_proceso', 'proceso_nombre')
            ->orderBy('cod_proceso')->get();


            foreach ($procesos as $proceso) {
                $resultado = array_merge($resultado, $this->getFlatProcessList($proceso));
            }

        }     

        return response()->json($resultado);
    }

    private function getFlatProcessList($proceso)
    {
        $resultado = [
            [
                'id' => $proceso->id,
                'proceso_nombre' => $proceso->cod_proceso . ' - ' . $proceso->proceso_nombre
            ]
        ];

        foreach ($proceso->subprocesos as $subproceso) {
            $resultado = array_merge($resultado, $this->getFlatProcessList($subproceso));
        }

        usort($resultado, function($a, $b) {
            return strcmp($a['proceso_nombre'], $b['proceso_nombre']);
        });

        return $resultado;
    }

    public function listar()
    {

        $procesos = Proceso::all();

        return response()->json($procesos);
    }
    public function mapaProcesos()
    {

        $procesos = Proceso::whereNull('cod_proceso_padre')->orderBy('proceso_tipo')->get();

        return view('procesos.mapa', compact('procesos'));

    }

    public function listarOUO($proceso_id)
    {
        // Lógica para asociar procesos, por ejemplo:
        $proceso = Proceso::findOrFail($proceso_id);

        // Aquí agregarías la lógica necesaria para asociar procesos
      
        $ouos = $proceso->ouos->map(function($ouo) use ($proceso_id) {
            // Añadir el proceso_id a cada OUO para usarlo en el frontend
            $ouo->proceso_id = $proceso_id;
            return $ouo;
        }); // Obtén las OUO asociadas al proceso

        return response()->json($ouos);
    }

    public function asociarOUO(Request $request, $proceso_id)
    {
        $ouo_id = $request->input('ouo_id');  // Obtener el ID de la OUO seleccionada

        $proceso = Proceso::findOrFail($proceso_id);
        
        
        $proceso->ouos()->attach($ouo_id);  

        $ouos = $proceso->ouos;

        // Redirigir a la vista de asociación
        return response()->json([
            'message' => 'OUO asociada correctamente.',
            'ouos' => $ouos
        ]);
    }
    public function disociarOUO($proceso_id, $ouo_id)
    {
        // Encuentra el proceso por ID
        $proceso = Proceso::findOrFail($proceso_id);

        // Desvincular la OUO del proceso
        $proceso->ouos()->detach($ouo_id);

        // Obtener las OUOs asociadas actualizadas
        $ouos = $proceso->ouos;

        // Redirigir con un mensaje de éxito
        return response()->json([
            'message' => 'OUO eliminada correctamente.',
            'ouos' => $ouos
        ]);
    }
}