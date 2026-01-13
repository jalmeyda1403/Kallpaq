<?php

namespace App\Http\Controllers;

use App\Models\NormaAuditable;
use App\Models\RequisitoNorma;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NormaAuditableController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $normas = NormaAuditable::withCount('requisitos')->latest()->get();
        return response()->json($normas);
    }

    public function show($id)
    {
        $norma = NormaAuditable::with('requisitos')->findOrFail($id);
        return response()->json($norma);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'requisitos' => 'array'
        ]);

        return DB::transaction(function () use ($request) {
            $norma = NormaAuditable::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion
            ]);

            if ($request->has('requisitos')) {
                foreach ($request->requisitos as $req) {
                    $norma->requisitos()->create([
                        'numeral' => $req['numeral'],
                        'denominacion' => $req['denominacion'],
                        'detalle' => $req['detalle'] ?? null
                    ]);
                }
            }

            return response()->json($norma->load('requisitos'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $norma = NormaAuditable::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'requisitos' => 'array'
        ]);

        return DB::transaction(function () use ($request, $norma) {
            $norma->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion
            ]);

            if ($request->has('requisitos')) {
                // Simplest approach: Delete all and recreate. 
                // Alternatively, could be optimized to update existing.
                $norma->requisitos()->forceDelete();

                foreach ($request->requisitos as $req) {
                    $norma->requisitos()->create([
                        'numeral' => $req['numeral'],
                        'denominacion' => $req['denominacion'],
                        'detalle' => $req['detalle'] ?? null
                    ]);
                }
            }

            return response()->json($norma->load('requisitos'));
        });
    }

    public function destroy($id)
    {
        $norma = NormaAuditable::findOrFail($id);
        $norma->delete();
        return response()->json(null, 204);
    }

    public function generateRequirements(Request $request)
    {
        $request->validate(['nombre_norma' => 'required|string']);

        try {
            $requirements = $this->aiService->generateNormaRequirements($request->nombre_norma);
            return response()->json($requirements);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error generating requirements: ' . $e->getMessage()], 500);
        }
    }
}
