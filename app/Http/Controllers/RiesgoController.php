<?php

namespace App\Http\Controllers;
use App\Models\Riesgo;
use App\Models\Obligacion;
use Illuminate\Http\Request;

class RiesgoController extends Controller
{
    // Mostrar una lista de los riesgos
    public function index()
    {
     
        $riesgos = Riesgo::all();
        return view('riesgos.index', compact('riesgos'));
    }

   
    // Almacenar un nuevo riesgo en la base de datos
    public function store(Request $request)
    {
                  // Crear el nuevo riesgo en la base de datos
         
        $obligacion = Obligacion::find($request->obligacion_id);
        $proceso_id = $obligacion->proceso_id;
        $request->merge(['proceso_id' => $proceso_id]);
        $riesgo = Riesgo::create($request->all());
        
        $obligacion->asociarRiesgo($riesgo);     
        $riesgos = $obligacion->riesgos;
        return response()->json($riesgos);
    
    }

    // Mostrar un riesgo específico
    public function show(Riesgo $id)
    {
        $riesgo = Riesgo::firstOrFail($id);
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
        $request->validate([
            'codigo' => 'required|unique:riesgos,codigo,' . $riesgo->id,
            'nombre' => 'required',
            'tipo' => 'required',
            'valor' => 'required|numeric',
            'valoracion' => 'required|numeric',
        ]);

        $riesgo->update($request->all());

        return redirect()->route('riesgos.index')->with('success', 'Riesgo actualizado exitosamente.');
    }

    // Eliminar un riesgo de la base de datos
    public function destroy(Riesgo $riesgo)
    {
        $riesgo->delete();

        return redirect()->route('riesgos.index')->with('success', 'Riesgo eliminado exitosamente.');
    }
}