<?php

namespace App\Http\Controllers;
use App\Models\Inventario;
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
        if (($request->has('proceso_padre_id') && $request->proceso_padre_id != '') or ($request->has('buscar_proceso') && $request->buscar_proceso != '')) {
            // Filtrar si se selecciona un proceso padre
            if ($request->has('proceso_padre_id') && $request->proceso_padre_id != '') {
                $query->where('cod_proceso_padre', $request->proceso_padre_id);
            }
    
            // Buscar por nombre de proceso si se proporciona
            if ($request->has('buscar_proceso') && $request->buscar_proceso != '') {
                $query->where('proceso_nombre', 'LIKE', "%{$request->buscar_proceso}%");
            }
    
            // En caso de que alguno de los dos esté presente, filtrar también por niveles 0 y 1
            $query->whereIn('proceso_nivel', [0, 1, 2]);
        } else {
            // Si no se proporciona ningún filtro, solo filtrar por nivel 0
            $query->whereIn('proceso_nivel', [0]);
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

        if ($procesos->count() > 0) 
        {
            return view('procesos.subprocesos', compact('procesos', 'proceso_padre'));
        }else{
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

    public function update(Request $request, $id)
    {
        $proceso = Proceso::findOrFail($id);
        $proceso->update($request->all());

        return redirect()->route('procesos.index')->with('success', 'Proceso actualizado correctamente');
    }

    public function destroy($id)
    {
        $proceso = Proceso::findOrFail($id);
        $proceso->delete();
        return response()->json(['success' => true, 'message' => 'Proceso eliminado exitosamente.']);
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

        usort($resultado, function ($a, $b) {
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
        $inventarios = Inventario::all();
        $procesos = Proceso::whereNull('cod_proceso_padre')->orderBy('proceso_tipo')->get();
        return view('procesos.mapa', compact('inventarios','procesos'));

    }

    public function listarOUO($proceso_id)
    {
        // Lógica para asociar procesos, por ejemplo:
        $proceso = Proceso::findOrFail($proceso_id);

        // Aquí agregarías la lógica necesaria para asociar procesos

        $ouos = $proceso->ouos->map(function ($ouo) use ($proceso_id) {
            // Añadir el proceso_id a cada OUO para usarlo en el frontend
            $ouo->proceso_id = $proceso_id;
            return $ouo;
        }); // Obtén las OUO asociadas al proceso

        return response()->json($ouos);
    }

    
}