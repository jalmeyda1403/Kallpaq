<?php

namespace App\Http\Controllers;
use App\Models\Salida;
use Illuminate\Http\Request;
use Carbon\Carbon;
class SipocController extends Controller
{
    // Función para cargar los requisitos de una salida
    public function getRequisitos($salida_id)
    {
        // Buscar la salida por su ID
        $salida = Salida::with('requisitos')->findOrFail($salida_id);

        // Retornar la vista parcial con los requisitos
        return response()->json([
            'requisitos' => $salida->requisitos->map(function ($requisito) {
                return [
                    'descripcion' => $requisito->requisito,
                    'documento' => $requisito->documento,
                    'fecha_requisito' => Carbon::parse($requisito->fecha_requisito)->format('d-m-Y') // Usamos Carbon para formatear
                ];
                
            })
        ]);
    }
}
