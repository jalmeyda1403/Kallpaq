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
        $programa = ProgramaAuditoria::with(['auditoriasEspecificas'])->find($id);

        if (!$programa) {
            return response()->json(['message' => 'Programa no encontrado'], 404);
        }

        return response()->json($programa);
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

    public function aprobar(ProgramaAuditoria $programa)
    {
        // Lógica para aprobar el programa de auditoría
        // Aquí podrías bloquear el programa y realizar otras acciones necesarias

        // Redirigir a la lista de programas de auditoría con un mensaje de éxito
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría aprobado exitosamente.');
    }

    public function reprogramar(ProgramaAuditoria $programa)
    {
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría reprogramado exitosamente.');
    }

    public function showHistory(ProgramaAuditoria $programa)
    {
        // Obtener el historial de cambios de estados para el programa de auditoría
        //  $cambios_estado = $programa->cambiosEstado;

        //return view('programa_auditoria.history', compact('programa', 'cambios_estado'));
    }
}
