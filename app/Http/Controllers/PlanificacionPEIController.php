<?php

namespace App\Http\Controllers;
use App\Models\PlanificacionPEI;
use Illuminate\Http\Request;

class PlanificacionPEIController extends Controller
{
    public function findObjetivosPEI(Request $request)
    {
    $query = $request->input('query');

    $objetivos = PlanificacionPEI::select('id','planificacion_pei_nombre')->where('planificacion_pei_nombre', 'LIKE', "%{$query}%")->get();

    return response()->json($objetivos);
    }
}
