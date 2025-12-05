<?php

namespace App\Http\Controllers;

use App\Models\Riesgo;
use App\Models\RiesgoAccion;
use App\Models\RiesgoAccionReprogramacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RiesgoAccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($riesgoId)
    {
        $acciones = RiesgoAccion::where('riesgo_id', $riesgoId)
            ->with('reprogramaciones')
            ->get();
        return response()->json($acciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $riesgoId)
    {
        $request->validate([
            'ra_descripcion' => 'required|string',
            'ra_responsable' => 'required',
            'ra_fecha_inicio' => 'required|date',
            'ra_fecha_fin_planificada' => 'required|date|after_or_equal:ra_fecha_inicio',
        ]);

        $accion = new RiesgoAccion($request->all());
        $accion->riesgo_id = $riesgoId;
        $accion->ra_estado = 'programada';
        $accion->save();

        return response()->json($accion, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $accion = RiesgoAccion::findOrFail($id);

        $request->validate([
            'ra_descripcion' => 'required|string',
            'ra_responsable' => 'required',
            'ra_fecha_inicio' => 'required|date',
            'ra_fecha_fin_planificada' => 'required|date|after_or_equal:ra_fecha_inicio',
        ]);

        $accion->update($request->all());
        return response()->json($accion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $accion = RiesgoAccion::findOrFail($id);
        $accion->delete();
        return response()->json(['message' => 'Acción eliminada correctamente']);
    }

    /**
     * Solicitar reprogramación de una acción.
     */
    public function reprogramar(Request $request, $id)
    {
        $request->validate([
            'fecha_nueva' => 'required|date|after:today',
            'justificacion' => 'required|string',
            'evidencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        $accion = RiesgoAccion::findOrFail($id);

        $path = null;
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('riesgos/acciones/reprogramaciones', 'public');
        }

        $reprogramacion = new RiesgoAccionReprogramacion();
        $reprogramacion->riesgo_accion_id = $accion->id;
        $reprogramacion->rar_fecha_anterior = $accion->ra_fecha_fin_planificada;
        $reprogramacion->rar_fecha_nueva = $request->fecha_nueva;
        $reprogramacion->rar_justificacion = $request->justificacion;
        $reprogramacion->rar_evidencia = $path;
        $reprogramacion->rar_estado = 'Pendiente';
        $reprogramacion->save();

        // Actualizar estado de la acción si es necesario
        // $accion->ra_estado = 'En Reprogramación'; // Opcional, dependiendo de la lógica de negocio
        // $accion->save();

        return response()->json($reprogramacion, 201);
    }

    /**
     * Aprobar reprogramación.
     */
    public function aprobarReprogramacion(Request $request, $id)
    {
        $reprogramacion = RiesgoAccionReprogramacion::findOrFail($id);
        $accion = $reprogramacion->riesgoAccion;

        $reprogramacion->rar_estado = 'Aprobado';
        $reprogramacion->rar_aprobado_por = Auth::id();
        $reprogramacion->rar_fecha_aprobacion = now();
        $reprogramacion->rar_comentario_aprobacion = $request->comentario;
        $reprogramacion->save();

        // Actualizar la fecha de la acción
        $accion->ra_fecha_fin_reprogramada = $reprogramacion->rar_fecha_nueva;
        $accion->save();

        return response()->json(['message' => 'Reprogramación aprobada', 'accion' => $accion]);
    }

    /**
     * Rechazar reprogramación.
     */
    public function rechazarReprogramacion(Request $request, $id)
    {
        $reprogramacion = RiesgoAccionReprogramacion::findOrFail($id);

        $reprogramacion->rar_estado = 'Rechazado';
        $reprogramacion->rar_aprobado_por = Auth::id();
        $reprogramacion->rar_fecha_aprobacion = now();
        $reprogramacion->rar_comentario_aprobacion = $request->comentario;
        $reprogramacion->save();

        return response()->json(['message' => 'Reprogramación rechazada']);
    }

    /**
     * Actualizar avance de la acción (cerrar acción).
     */
    public function updateAvance(Request $request, $id)
    {
        $request->validate([
            'ra_estado' => 'required|in:programada,desestimada,en proceso,implementada',
            'ra_evidencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        $accion = RiesgoAccion::findOrFail($id);

        $data = $request->only(['ra_estado', 'ra_comentario']);

        if ($request->hasFile('ra_evidencia')) {
            // Eliminar evidencia anterior si existe
            if ($accion->ra_evidencia) {
                Storage::disk('public')->delete($accion->ra_evidencia);
            }
            $data['ra_evidencia'] = $request->file('ra_evidencia')->store('riesgos/acciones/evidencias', 'public');
        }

        if ($request->ra_estado === 'implementada') {
            $data['ra_fecha_fin_real'] = now();
        }

        $accion->update($data);

        return response()->json($accion);
    }
}
