<?php

namespace App\Http\Controllers;
use App\Models\OUO;
use Illuminate\Http\Request;

class OUOController extends Controller
{
    public function buscar()
    {
        $ouos = OUO::select('id', 'ouo_nombre AS descripcion')->get();
        return response()->json($ouos);
    }
}
