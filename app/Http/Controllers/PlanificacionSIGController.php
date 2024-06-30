<?php

namespace App\Http\Controllers;
use App\Models\PlanificacionSIG;
use Illuminate\Http\Request;

class PlanificacionSIGController extends Controller
{
    public function findObjetivos(Request $request)
    {
    $query = $request->input('query');

    $objetivos = PlanificacionSIG::where('nombre_objetivo', 'LIKE', "%{$query}%")->get();

    return response()->json($objetivos);
    }
}
