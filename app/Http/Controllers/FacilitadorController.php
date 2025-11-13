<?php

namespace App\Http\Controllers;

use App\Models\Facilitador;
use App\Models\User;
use App\Models\Proceso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacilitadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Facilitador::with('user', 'procesos');

        // Server-side filtering
        if ($request->has('name') && $request->name) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        $facilitadores = $query->get();
        return response()->json($facilitadores);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id', Rule::unique('facilitadores', 'user_id')],
            'cargo' => ['required', 'string', 'in:facilitador,propietario'],
            'estado' => ['required', 'string', 'in:activo,inactivo'],
        ]);

        $facilitador = Facilitador::create($validatedData);
        return response()->json($facilitador->load('user'), 201);
    }

    public function show(Facilitador $facilitador)
    {
        return response()->json($facilitador->load('user', 'procesos'));
    }

    public function update(Request $request, Facilitador $facilitador)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id', Rule::unique('facilitadores', 'user_id')->ignore($facilitador->id)],
            'cargo' => ['required', 'string', 'in:facilitador,propietario'],
            'estado' => ['required', 'string', 'in:activo,inactivo'],
        ]);

        $facilitador->update($validatedData);
        return response()->json($facilitador->load('user'));
    }

    public function destroy(Facilitador $facilitador)
    {
        $facilitador->delete();
        return response()->json(['message' => 'Facilitador eliminado con éxito.'], 204);
    }

    // Métodos para asociar/disociar procesos
    public function attachProceso(Request $request, Facilitador $facilitador)
    {
        $request->validate([
            'proceso_id' => ['required', 'exists:procesos,id'],
        ]);

        if ($facilitador->procesos()->where('proceso_id', $request->proceso_id)->exists()) {
            return response()->json(['message' => 'El facilitador ya está asociado a este proceso.'], 409);
        }

        $facilitador->procesos()->attach($request->proceso_id);
        return response()->json(['message' => 'Proceso asociado con éxito.'], 200);
    }

    public function detachProceso(Facilitador $facilitador, Proceso $proceso)
    {
        $facilitador->procesos()->detach($proceso->id);
        return response()->json(['message' => 'Proceso disociado con éxito.'], 200);
    }

    public function listProcesos(Facilitador $facilitador)
    {
        return response()->json($facilitador->procesos);
    }
}