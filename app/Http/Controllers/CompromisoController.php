<?php

namespace App\Http\Controllers;

use App\Models\Compromiso;
use Illuminate\Http\Request;

class CompromisoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expectativa_id' => 'required|exists:expectativas,id',
            'ec_descripcion' => 'required|string',
            'ec_responsable_id' => 'nullable|exists:users,id',
            'ec_fecha_limite' => 'nullable|date',
            'ec_estado' => 'required|in:pendiente,en_proceso,completado',
            'ec_avance' => 'nullable|string',
        ]);

        $compromiso = Compromiso::create($validated);

        return response()->json(['message' => 'Compromiso creado', 'compromiso' => $compromiso], 201);
    }

    public function update(Request $request, $id)
    {
        $compromiso = Compromiso::findOrFail($id);

        $validated = $request->validate([
            'ec_descripcion' => 'required|string',
            'ec_responsable_id' => 'nullable|exists:users,id',
            'ec_fecha_limite' => 'nullable|date',
            'ec_estado' => 'required|in:pendiente,en_proceso,completado',
            'ec_avance' => 'nullable|string',
        ]);

        $compromiso->update($validated);

        return response()->json(['message' => 'Compromiso actualizado', 'compromiso' => $compromiso]);
    }

    public function destroy($id)
    {
        $compromiso = Compromiso::findOrFail($id);
        $compromiso->delete();
        return response()->json(['message' => 'Compromiso eliminado']);
    }
}
