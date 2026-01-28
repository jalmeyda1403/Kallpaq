<?php

namespace App\Http\Controllers;

use App\Models\Control;
use Illuminate\Http\Request;

class ControlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Control::orderBy('nombre', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:Preventivo,Detectivo,Correctivo',
            'frecuencia' => 'nullable|string',
            'responsable' => 'nullable|string',
            'estado' => 'in:Activo,Inactivo'
        ]);

        $control = Control::create($validated);

        return response()->json([
            'message' => 'Control creado exitosamente',
            'control' => $control
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Control $control)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Control $control)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Control $control)
    {
        //
    }
}
