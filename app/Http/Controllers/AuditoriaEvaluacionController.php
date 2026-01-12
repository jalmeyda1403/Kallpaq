<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaEvaluacion;
use App\Models\AuditoriaEspecifica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriaEvaluacionController extends Controller
{
    public function index($ae_id)
    {
        $evaluaciones = AuditoriaEvaluacion::where('ae_id', $ae_id)
            ->with(['evaluador', 'evaluado'])
            ->get();
        return response()->json($evaluaciones);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ae_id' => 'required|exists:auditoria_especifica,id',
            'evaluado_id' => 'required|exists:users,id',
            'aev_rol_evaluado' => 'required',
            'aev_criterios' => 'required|array', // JSON input
            'aev_promedio' => 'required|numeric',
        ]);

        $evaluacion = AuditoriaEvaluacion::create([
            'ae_id' => $request->ae_id,
            'evaluador_id' => Auth::id(), // Evaluator is the logged in user
            'evaluado_id' => $request->evaluado_id,
            'aev_rol_evaluado' => $request->aev_rol_evaluado,
            'aev_criterios' => $request->aev_criterios,
            'aev_promedio' => $request->aev_promedio,
            'aev_comentario' => $request->input('aev_comentario'),
        ]);

        return response()->json($evaluacion, 201);
    }

    // Generación de Constancia (Backend logic stub)
    public function generarConstancia($ae_id, $auditor_id)
    {
        $auditoria = AuditoriaEspecifica::with(['programa'])->findOrFail($ae_id);
        // Logic to generate PDF using DomPDF
        // $pdf = PDF::loadView('auditoria.constancia', compact('auditoria', ...));
        // return $pdf->download('constancia.pdf');

        return response()->json(['message' => 'Generación de constancia pendiente de implementación PDF']);
    }
}
