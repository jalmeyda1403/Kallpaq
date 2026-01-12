<?php

namespace App\Http\Controllers;

use App\Models\ParteInteresada;
use App\Models\Expectativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParteInteresadaController extends Controller
{
    public function index(Request $request)
    {
        $query = ParteInteresada::query()->with(['expectativas.compromisos', 'expectativas.procesos']);

        // Filtro por tipo
        if ($request->has('pi_tipo') && $request->pi_tipo) {
            $query->where('pi_tipo', $request->pi_tipo);
        }

        // Filtro por cuadrante (calculado en BD o filtrado post-fetch? Mejor post-fetch si es complejo, pero intetemos BD si podemos)
        // El cuadrante es calculado, así que mejor filtramos en colección o añadimos columna generada. 
        // Por ahora devolvemos todo y el frontend filtra.
        
        $partes = $query->orderBy('pi_nombre')->get();
        
        // Append computed attributes
        $partes->each(function ($parte) {
            $parte->append(['cuadrante', 'valoracion', 'mensaje']);
        });

        return response()->json($partes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pi_nombre' => 'required|string|max:255',
            'pi_tipo' => 'required|in:interna,externa,cliente,proveedor,regulador',
            'pi_nivel_influencia' => 'required|in:bajo,medio,alto',
            'pi_nivel_interes' => 'required|in:bajo,medio,alto',
            'pi_descripcion' => 'nullable|string',
        ]);

        $parte = ParteInteresada::create($validated);

        return response()->json(['message' => 'Parte interesada creada', 'parte' => $parte], 201);
    }

    public function update(Request $request, $id)
    {
        $parte = ParteInteresada::findOrFail($id);

        $validated = $request->validate([
            'pi_nombre' => 'required|string|max:255',
            'pi_tipo' => 'required|in:interna,externa,cliente,proveedor,regulador',
            'pi_nivel_influencia' => 'required|in:bajo,medio,alto',
            'pi_nivel_interes' => 'required|in:bajo,medio,alto',
            'pi_descripcion' => 'nullable|string',
        ]);

        $parte->update($validated);

        return response()->json(['message' => 'Parte interesada actualizada', 'parte' => $parte]);
    }

    public function destroy($id)
    {
        $parte = ParteInteresada::findOrFail($id);
        $parte->delete();
        return response()->json(['message' => 'Parte interesada eliminada']);
    }
}
