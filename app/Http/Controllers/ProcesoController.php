<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Proceso;
use App\Models\User;   

class ProcesoController extends Controller

{
    public function index()
    {
        $procesos = Proceso::all();
        return view('procesos.index', compact('procesos'));
    }

    public function create()
    {
        return view('procesos.create');
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
   
}