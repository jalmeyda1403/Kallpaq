<?php

namespace App\Http\Controllers;
use App\Models\PlanificacionPEI;
use Illuminate\Http\Request;

class PlanificacionPEIController extends Controller
{
    public function findObjetivosPEI(Request $request)
    {
        $query = $request->input('query');

        $objetivos = PlanificacionPEI::where(function ($q) use ($query) {
            $q->where('pp_nombre', 'LIKE', "%{$query}%")
                ->orWhere('pp_cod', 'LIKE', "%{$query}%");
        })->get();

        $objetivos->map(function ($objetivo) {
            return [
                'id' => $objetivo->id,
                'descripcion' => $objetivo->descripcion,
            ];
        });

        return response()->json($objetivos);
    }
}
