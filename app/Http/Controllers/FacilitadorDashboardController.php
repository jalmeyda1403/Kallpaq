<?php

namespace App\Http\Controllers;

use App\Models\Facilitador;
use App\Models\Hallazgo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilitadorDashboardController extends Controller
{
    public function getHallazgos(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->facilitador) {
            return response()->json(['message' => 'Usuario no autenticado o no es un facilitador.'], 403);
        }

        $facilitador = $user->facilitador;
        $procesoIds = $facilitador->procesos->pluck('id');

        if ($procesoIds->isEmpty()) {
            return response()->json(['message' => 'El facilitador no tiene procesos asignados.'], 200);
        }

        $hallazgos = Hallazgo::whereHas('hallazgoProcesos', function ($query) use ($procesoIds) {
            $query->whereIn('proceso_id', $procesoIds);
        })->with('hallazgoProcesos.proceso', 'acciones', 'causa')->get(); // Eager load related data

        return response()->json($hallazgos);
    }
}