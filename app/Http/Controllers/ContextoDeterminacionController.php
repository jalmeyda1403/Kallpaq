<?php

namespace App\Http\Controllers;
use App\Models\ContextoDeterminacion;
use App\Models\Proceso;
use Illuminate\Http\Request;


class ContextoDeterminacionController extends Controller
{
    public function index($procesoId = null)
    {
        if ($procesoId)
        {
            $proceso = Proceso::with('contextoDeterminaciones')->findOrFail($procesoId);
        }
        else{
            $contexto = ContextoDeterminacion::All();
        }
        return view('contexto.index', compact('contexto'));
    }
}
