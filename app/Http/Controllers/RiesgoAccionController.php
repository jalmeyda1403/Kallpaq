<?php

namespace App\Http\Controllers;

use App\Models\RiesgoAccion;
use App\Models\Riesgo;
use Illuminate\Http\Request;

class RiesgoAccionController extends Controller
{
    public function index($riesgoId)
    {
        $acciones = RiesgoAccion::where('riesgo_id', $riesgoId)->get();
        return response()->json($acciones);
    }

    public function store(Request $request, $riesgoId)
    {
        $validated = $request->validate([
            'ra_descripcion' => 'required|string',
            'ra_comentario' => 'nullable|string',
            'ra_fecha_inicio' => 'nullable|date',
            'ra_fecha_fin_planificada' => 'nullable|date',
            'ra_responsable' => 'nullable|string',
            'ra_responsable_correo' => 'nullable|email',
            'ra_estado' => 'required|in:programada,desestimada,en proceso,implementada',
        ]);

        $validated['riesgo_id'] = $riesgoId;
        $validated['ra_ciclo'] = 1; // Default cycle

        $accion = RiesgoAccion::create($validated);

        return response()->json($accion, 201);
    }

    public function update(Request $request, $id)
    {
        $accion = RiesgoAccion::findOrFail($id);

        $validated = $request->validate([
            'ra_descripcion' => 'required|string',
            'ra_comentario' => 'nullable|string',
            'ra_fecha_inicio' => 'nullable|date',
            'ra_fecha_fin_planificada' => 'nullable|date',
            'ra_fecha_fin_reprogramada' => 'nullable|date',
            'ra_fecha_fin_cancelada' => 'nullable|date',
            'ra_fecha_fin_real' => 'nullable|date',
            'ra_justificacion' => 'nullable|string',
            'ra_evidencia' => 'nullable|string',
            'ra_responsable' => 'nullable|string',
            'ra_responsable_correo' => 'nullable|email',
            'ra_estado' => 'required|in:programada,desestimada,en proceso,implementada',
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

    public function reprogramar(Request $request, $id)
    {
        $accion = RiesgoAccion::findOrFail($id);

        $request->validate([
            'actionType' => 'required|string|in:reprogramar,desestimar',
            'ra_justificacion' => 'required|string',
            'ra_fecha_fin_reprogramada' => 'required_if:actionType,reprogramar|date',
            'ra_evidencia' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $accion->ra_justificacion = $request->ra_justificacion;

        if ($request->hasFile('ra_evidencia')) {
            $file = $request->file('ra_evidencia');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('riesgos/acciones/' . $id, $filename, 'public');
            $accion->ra_evidencia = $path;
        }

        if ($request->actionType === 'reprogramar') {
            $accion->ra_fecha_fin_reprogramada = $request->ra_fecha_fin_reprogramada;
        } else { // desestimar
            $accion->ra_estado = 'desestimada';
        }

        $accion->save();

        return response()->json(['message' => 'Acción gestionada con éxito.', 'accion' => $accion]);
    }
}
