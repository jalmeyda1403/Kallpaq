<?php

namespace App\Http\Controllers;

use App\Models\ProgramaAuditoria;
use Illuminate\Http\Request;

class ProgramaAuditoriaController extends Controller
{
    public function index()
    {
        $programas = ProgramaAuditoria::all();
        return view('programa.index', compact('programas'));
    }

    public function apiIndex(Request $request)
    {
        $query = ProgramaAuditoria::select([
            'id',
            'pa_anio',
            'pa_version',
            'pa_estado',
            'pa_fecha_aprobacion',
            'pa_recursos',
            'archivo_pdf',
            'pa_objetivo_general',
            'pa_alcance',
            'pa_metodos',
            'pa_criterios'
        ]);


        if ($request->has('year') && $request->year != '') {
            $query->where('pa_anio', $request->year);
        }

        $programas = $query->orderBy('pa_anio', 'desc')->get();
        return response()->json($programas);
    }

    public function apiShow($id)
    {
        $programa = ProgramaAuditoria::select([
            'id',
            'pa_anio',
            'pa_version',
            'pa_estado',
            'pa_fecha_aprobacion',
            'pa_recursos',
            'pa_objetivo_general',
            'pa_alcance',
            'pa_metodos',
            'pa_criterios'
        ])->with([

                    'auditoriasEspecificas' => function ($query) {
                        $query->select([
                            'id',
                            'pa_id',
                            'ae_codigo',
                            'ae_tipo',
                            'ae_alcance',
                            'ae_sistema',
                            'ae_ciclo',
                            'ae_horas_hombre',
                            'ae_fecha_inicio',
                            'ae_fecha_fin',
                            'ae_estado',
                            'ae_avance' // Select persisted progress
                        ]);
                    },
                    'auditoriasEspecificas.procesos:id,proceso_nombre'
                ])->find($id);


        if (!$programa) {
            return response()->json(['message' => 'Programa no encontrado'], 404);
        }

        // Calculate Compliance based on executed status (Performance optimized)
        // We no longer calculate individual audit progress here as it is persisted in ae_avance
        $audits = $programa->auditoriasEspecificas;

        $total = $audits->count();
        $cancelled = $audits->where('ae_estado', 'Cancelada')->count();
        $executed = $audits->whereIn('ae_estado', ['Ejecutada', 'Cerrada'])->count();

        $denominator = $total - $cancelled;
        $compliance = $denominator > 0 ? round(($executed / $denominator) * 100, 2) : 0;

        $programa->compliance = $compliance;
        // $programa->auditorias_especificas is already set by relation

        return response()->json($programa);

    }

    public function changeStatus(Request $request, $id)
    {
        $programa = ProgramaAuditoria::with('auditoriasEspecificas')->findOrFail($id);
        $status = $request->input('status'); // Aprobada, Cerrada, Borrador?

        if ($status === 'Cerrada') {
            // Validate all audits are closed/executed/cancelled
            $pending = $programa->auditoriasEspecificas->filter(function ($audit) {
                return !in_array($audit->ae_estado, ['Ejecutada', 'Cerrada', 'Cancelada']);
            });

            if ($pending->count() > 0) {
                return response()->json([
                    'message' => 'No se puede cerrar el programa. Hay auditorías pendientes.',
                    'pending_audits' => $pending->pluck('ae_codigo')
                ], 422);
            }
        }

        $programa->pa_estado = $status;
        $programa->save();

        return response()->json(['message' => "Estado actualizado a $status", 'programa' => $programa]);
    }

    // ... (keep legacy methods if needed or refactor them)
    // Legacy MVC Actions
    public function aprobar(ProgramaAuditoria $programa)
    {
        $programa->update(['pa_estado' => 'Aprobada']);
        return redirect()->route('programa.index')->with('success', 'Programa aprobado.');
    }

    public function reprogramar(ProgramaAuditoria $programa)
    {
        $programa->update(['pa_estado' => 'Borrador']); // Reset to draft?
        return redirect()->route('programa.index')->with('success', 'Programa reprogramado.');
    }

    public function create()
    {
        return view('programa.create');
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'pa_version' => 'required',
            'pa_anio' => 'required',
            'pa_fecha_aprobacion' => 'required',
        ]);

        // Crear un nuevo programa de auditoría
        $programa = ProgramaAuditoria::create($request->all());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Programa creado', 'data' => $programa]);
        }

        // Redirigir a la lista de programas de auditoría con un mensaje de éxito
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría creado exitosamente.');
    }

    public function edit(ProgramaAuditoria $programa)
    {
        return view('programa.edit', compact('programa'));
    }

    public function update(Request $request, ProgramaAuditoria $programa)
    {
        // Validar los datos del formulario
        $request->validate([
            'pa_version' => 'required',
            'pa_anio' => 'required',
            'pa_fecha_aprobacion' => 'required',
        ]);

        // Actualizar el programa de auditoría
        $programa->update($request->all());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Programa actualizado', 'data' => $programa]);
        }

        // Redirigir a la lista de programas de auditoría con un mensaje de éxito
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría actualizado exitosamente.');
    }



    public function showHistory(ProgramaAuditoria $programa)
    {
        // Obtener el historial de cambios de estados para el programa de auditoría
        //  $cambios_estado = $programa->cambiosEstado;

        //return view('programa_auditoria.history', compact('programa', 'cambios_estado'));
    }
}
