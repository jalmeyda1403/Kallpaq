<?php

namespace App\Http\Controllers;
use App\Models\Sipoc;
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
    public function update(Request $request, $id)
    {
     
        $sipoc = Sipoc::findOrFail($id);
        $sipoc->update($request->all());

        return redirect()->back()->with('success', 'SIPOC actualizado correctamente');

    }
    public function store(Request $request)
    {
    
        $sipoc = Sipoc::create($request->all());
        return redirect()->back()->with('success', 'SIPOC actualizado correctamente');
    }


}
