<?php

namespace App\Http\Controllers;
use App\Models\PlanificacionSIG;
use Illuminate\Http\Request;

class PlanificacionSIGController extends Controller
{
    public function findObjetivosSIG(Request $request)
    {
    $query = $request->input('query');

    $objetivos = PlanificacionSIG::select('id', 'objetivo_sig_nombre')->get();

    return response()->json($objetivos);
    }
}
