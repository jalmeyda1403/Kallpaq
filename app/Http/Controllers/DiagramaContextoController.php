<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use App\Models\DiagramaContexto;
use App\Models\Proceso;

class DiagramaContextoController extends Controller
{
    public function index($proceso_id)
    {
        // Recuperamos el diagrama de contexto actual (si existe)
        $diagrama = DiagramaContexto::where('proceso_id', $proceso_id)->where('estado', 'activo')->first();
        $proceso = Proceso::findOrFail($proceso_id);
        $subprocesos = $proceso->subprocesos;
        $sipocs = $proceso->sipoc;

        // Join salidas
        $salidas_padre = $proceso->salidas;
        $salidas_subproceso = $subprocesos->flatMap(function ($subproceso) {
            return $subproceso->salidas; // Accedemos a las salidas de cada subproceso
        });
        $salidas = $salidas_padre->merge($salidas_subproceso);

        // Join documentos
        $documentos_padre = $proceso->documentos;
        $documentos_subproceso = $subprocesos->flatMap(function ($subproceso) {
            return $subproceso->documentos; // Accedemos a las salidas de cada subproceso
        });

        $documentos = $documentos_padre->merge($documentos_subproceso);

        // Join procesos   
        $procesos = collect([$proceso])->merge($subprocesos);
        
        return view('procesos.caracterizacion', compact('diagrama', 'documentos', 'salidas', 'sipocs', 'procesos', 'proceso', 'proceso_id'));
    }

    public function store(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif',
            'version' => 'required|string',
            'vigencia' => 'required|date',
            'proceso_id' => 'required|integer',
        ]);

        // Subir el archivo
        $archivoPath = $request->file('archivo')->store('diagramas', 'public');

        // Crear el nuevo diagrama
        DiagramaContexto::create([
            'proceso_id' => $validated['proceso_id'],
            'archivo' => $archivoPath,
            'version' => $validated['version'],
            'vigencia' => $validated['vigencia'],
        ]);

        return redirect()->route('procesos.caracterizacion')->with('success', 'Diagrama de contexto actualizado');
    }

    public function show()
    {

    }

    public function update(Request $request, $id)
    {
        // Validar los datos
        $validated = $request->validate([
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif',
            'version' => 'required|string',
            'vigencia' => 'required|date',
        ]);

        // Buscar el diagrama por ID
        $diagrama = DiagramaContexto::findOrFail($id);

        // Eliminar el archivo anterior si se sube uno nuevo
        if ($request->hasFile('archivo')) {
            $diagrama->delete();  // Eliminar archivo anterior
            $archivoPath = $request->file('archivo')->store('diagramas', 'public');
            $diagrama->archivo = $archivoPath;
        }

        // Actualizar los datos
        $diagrama->version = $validated['version'];
        $diagrama->vigencia = $validated['vigencia'];
        $diagrama->save();

        return redirect()->route('procesos.caracterizacion')->with('success', 'Diagrama de contexto actualizado');
    }
}
