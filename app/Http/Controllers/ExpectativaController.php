<?php

namespace App\Http\Controllers;

use App\Models\Expectativa;
use Illuminate\Http\Request;

class ExpectativaController extends Controller
{
    public function store(Request $request)
    {
        // Extract IDs from procesos if they are objects
        $procesosIds = $this->extractIds($request->input('procesos', []));
        
        $validated = $request->validate([
            'parte_interesada_id' => 'required|exists:partes_interesadas,id',
            'exp_descripcion' => 'required|string',
            'exp_tipo' => 'required|in:necesidad,expectativa',
            'exp_normas' => 'nullable|array', // JSON array
            'exp_criticidad' => 'required|integer|min:1|max:5',
            'exp_viabilidad' => 'required|integer|min:1|max:5',
            'exp_estado' => 'nullable|in:pendiente,en_proceso,implementado',
            'exp_observaciones' => 'nullable|string',
        ]);

        $expectativa = Expectativa::create($validated);
        
        // Sync Relations with extracted IDs
        if (!empty($procesosIds)) {
            $expectativa->procesos()->sync($procesosIds);
        }

        $expectativa->load(['procesos', 'compromisos']);

        return response()->json(['message' => 'Expectativa creada', 'expectativa' => $expectativa], 201);
    }

    public function update(Request $request, $id)
    {
        $expectativa = Expectativa::findOrFail($id);

        // Extract IDs from procesos if they are objects
        $procesosIds = $this->extractIds($request->input('procesos', []));

        $validated = $request->validate([
            'exp_descripcion' => 'required|string',
            'exp_tipo' => 'required|in:necesidad,expectativa',
            'exp_normas' => 'nullable|array',
            'exp_criticidad' => 'required|integer|min:1|max:5',
            'exp_viabilidad' => 'required|integer|min:1|max:5',
            'exp_estado' => 'nullable|in:pendiente,en_proceso,implementado',
            'exp_observaciones' => 'nullable|string',
        ]);

        $expectativa->update($validated);

        // Sync Relations with extracted IDs
        if (!empty($procesosIds)) {
            $expectativa->procesos()->sync($procesosIds);
        }
        
        $expectativa->load(['procesos', 'compromisos']);

        return response()->json(['message' => 'Expectativa actualizada', 'expectativa' => $expectativa]);
    }

    public function destroy($id)
    {
        $expectativa = Expectativa::findOrFail($id);
        $expectativa->delete();
        return response()->json(['message' => 'Expectativa eliminada']);
    }

    public function show($id)
    {
        $expectativa = Expectativa::with(['procesos', 'compromisos'])->findOrFail($id);
        return response()->json($expectativa);
    }

    /**
     * Extract IDs from array that might contain objects or plain IDs
     */
    private function extractIds($items)
    {
        if (empty($items)) {
            return [];
        }

        return collect($items)->map(function ($item) {
            // If it's an object/array with 'id', extract it
            if (is_array($item) && isset($item['id'])) {
                return $item['id'];
            }
            // If it's already an ID, return it
            return $item;
        })->filter()->values()->toArray();
    }
}
