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
        $query = ProgramaAuditoria::with('auditoriasEspecificas');

        if ($request->has('year') && $request->year != '') {
            $query->where('pa_anio', $request->year);
        }

        $programas = $query->orderBy('pa_anio', 'desc')->get();
        return response()->json($programas);
    }

    public function apiShow($id)
    {
        $programa = ProgramaAuditoria::with(['auditoriasEspecificas.proceso', 'auditoriasEspecificas.equipo'])->find($id);

        if (!$programa) {
            return response()->json(['message' => 'Programa no encontrado'], 404);
        }

        // Calculate Compliance
        $total = $programa->auditoriasEspecificas->count();
        $cancelled = $programa->auditoriasEspecificas->where('ae_estado', 'Cancelada')->count();
        $executed = $programa->auditoriasEspecificas->whereIn('ae_estado', ['Ejecutada', 'Cerrada'])->count();

        $denominator = $total - $cancelled;
        $compliance = $denominator > 0 ? round(($executed / $denominator) * 100, 2) : 0;

        $programa->compliance = $compliance;

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
