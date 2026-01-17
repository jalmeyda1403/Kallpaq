<?php

namespace App\Http\Controllers;

use App\Models\NormaAuditable;
use App\Models\NormaRequisito;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NormaRequisitoImport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

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
                        'nr_numeral' => $req['numeral'] ?? $req['nr_numeral'] ?? '',
                        'nr_denominacion' => $req['denominacion'] ?? $req['nr_denominacion'] ?? '',
                        'nr_detalle' => $req['detalle'] ?? $req['nr_detalle'] ?? null
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
                        'nr_numeral' => $req['numeral'] ?? $req['nr_numeral'] ?? '',
                        'nr_denominacion' => $req['denominacion'] ?? $req['nr_denominacion'] ?? '',
                        'nr_detalle' => $req['detalle'] ?? $req['nr_detalle'] ?? null
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

    public function downloadTemplate(Request $request)
    {
        $data = [];

        if ($request->has('norma_id')) {
            $norma = NormaAuditable::with('requisitos')->find($request->norma_id);
            if ($norma && $norma->requisitos->count() > 0) {
                foreach ($norma->requisitos as $req) {
                    $data[] = [
                        'numeral' => $req->nr_numeral,
                        'denominacion' => $req->nr_denominacion,
                        'detalle' => $req->nr_detalle,
                    ];
                }
            }
        }

        if (empty($data)) {
            $data = [
                ['numeral' => '4.1', 'denominacion' => 'Comprensión de la organización', 'detalle' => 'Párrafo de ejemplo...'],
                ['numeral' => '4.2', 'denominacion' => 'Necesidades de partes interesadas', 'detalle' => 'Párrafo de ejemplo...'],
            ];
        }

        return Excel::download(new class ($data) implements FromArray, WithHeadings {
            protected $data;
            public function __construct(array $data)
            {
                $this->data = $data;
            }
            public function array(): array
            {
                return $this->data;
            }
            public function headings(): array
            {
                return ['numeral', 'denominacion', 'detalle'];
            }
        }, 'plantilla_requisitos_norma.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new NormaRequisitoImport();
            $data = Excel::toCollection($import, $request->file('file'))->first();

            // Format data to ensure it has the expected keys and handle potential heading issues
            $formatted = $data->map(function ($row) {
                return [
                    'nr_numeral' => $row['numeral'] ?? $row['nr_numeral'] ?? '',
                    'nr_denominacion' => $row['denominacion'] ?? $row['nr_denominacion'] ?? '',
                    'nr_detalle' => $row['detalle'] ?? $row['nr_detalle'] ?? ''
                ];
            });

            return response()->json($formatted);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al importar: ' . $e->getMessage()], 500);
        }
    }
}
