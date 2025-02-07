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

        // $procesos = Proceso::all();
        //  return view('procesos.index', compact('procesos'));}

        // Si se selecciona un proceso padre, filtramos los procesos por ese proceso padre
        if ($request->has('proceso_padre_id') && $request->proceso_padre_id != '') {
            $query->where('cod_proceso_padre', $request->proceso_padre_id);
        }
        $procesos = $query->get();
        // Obtener procesos de primer nivel (sin proceso padre)
        $procesos_padre = Proceso::whereNull('cod_proceso_padre')->get();

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

    public function findProcesos(Request $request)
    {
        $query = $request->input('query');
        $user = User::find(auth()->user()->id);
        // $procesos = Proceso::where('nombre', 'LIKE', "%{$query}%")->get();
        $procesos = $user->procesos;
        return response()->json($procesos);
    }

    public function listarProcesos()
    {
       
        $procesos = Proceso::all();

        return response()->json($procesos);
    }
    public function mapaProcesos()
    {

        $procesos = Proceso::whereNull('cod_proceso_padre')->orderBy('tipo_proceso')->get();

        return view('procesos.mapa', compact('procesos'));

    }

    public function listarOUO($proceso_id)
    {
        // Lógica para asociar procesos, por ejemplo:
        $proceso = Proceso::findOrFail($proceso_id);

        // Aquí agregarías la lógica necesaria para asociar procesos
        // Puedes devolver una vista o redirigir según lo necesites
        $ouos = $proceso->ouos;  // Obtén las OUO asociadas al proceso
        $allouos = OUO::all();
        return view('procesos.asociarOUO', compact('proceso', 'ouos','allouos'));
    }

    public function asociarOUO(Request $request, $proceso_id)
    {
        $ouo_ids = $request->input('ouos');  // Obtener el ID de la OUO seleccionada
       
        $proceso = Proceso::findOrFail($proceso_id);
        
        // Asociar la OUO al proceso
        if ($ouo_ids) {
            $proceso->ouos()->attach($ouo_ids);  // Asociar todas las OUOs seleccionadas
        }
    
         // Redirigir a la vista de asociación
        return redirect()->route('procesos.listarOUO', ['proceso_id' => $proceso_id])
                     ->with('success', 'OUOs asociadas correctamente.');
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
    return redirect()->route('procesos.listarOUO', ['proceso_id' => $proceso_id])
                     ->with('success', 'OUO eliminada correctamente.');
}
}