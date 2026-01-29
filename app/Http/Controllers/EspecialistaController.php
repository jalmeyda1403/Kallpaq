<?php

namespace App\Http\Controllers;

use App\Models\Especialista;
use Illuminate\Http\Request;

class EspecialistaController extends Controller
{
    public function index()
    {
        $especialistas = Especialista::with('user')->get();
        $especialistas = $especialistas->map(function ($especialista) {
            return [
                'id' => $especialista->user_id,
                'descripcion' => $especialista->nombres . '  ' . $especialista->apellido_paterno . '  ' . $especialista->apellido_materno,
            ];
        });
        return response()->json($especialistas);
    }
}
