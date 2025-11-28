<?php

namespace App\Http\Controllers;

use App\Models\RiesgoAccion;
use App\Models\Riesgo;
use Illuminate\Http\Request;

class RiesgoAccionController extends Controller
{
    public function index($riesgoId)
    {
        $acciones = RiesgoAccion::where('riesgo_cod', $riesgoId)->get();
        return response()->json($acciones);
    }

    public function store(Request $request, $riesgoId)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_prog_inicio' => 'nullable|date',
            'fecha_prog_fin' => 'nullable|date',
            'responsable' => 'nullable|string',
            'estado' => 'required|in:En Implementación,Pendiente,Implementado,Cancelado',
            'comentario' => 'nullable|string',
        ]);

        $validated['riesgo_cod'] = $riesgoId;

        $accion = RiesgoAccion::create($validated);

        return response()->json($accion, 201);
    }

    public function update(Request $request, $id)
    {
        $accion = RiesgoAccion::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_prog_inicio' => 'nullable|date',
            'fecha_prog_fin' => 'nullable|date',
            'responsable' => 'nullable|string',
            'estado' => 'required|in:En Implementación,Pendiente,Implementado,Cancelado',
            'comentario' => 'nullable|string',
        ]);

        $accion->update($validated);

        return response()->json($accion);
    }

    public function destroy($id)
    {
        $accion = RiesgoAccion::findOrFail($id);
        $accion->delete();

        return response()->json(null, 204);
    }
}
