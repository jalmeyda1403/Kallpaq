<?php

namespace App\Http\Controllers;
use App\Models\AreaCompliance;
use Illuminate\Http\Request;

class AreaComplianceController extends Controller
{
    public function findAreaCompliance(Request $request)
    {
        $resultado = [];
        $query = $request->input('query');

        $areas = AreaCompliance::select('id', 'area_compliance_nombre')->orderBy("id")->get();

        foreach ($areas as $area) {
            $resultado = array_merge($resultado, $this->getFlatList($area));
        }

        return response()->json($resultado);
    }


    private function getFlatList($area)
    {
        $resultado = [
            [
                'id' => $area->id,
                'area_compliance_nombre' => $area->id . ' - ' . $area->area_compliance_nombre
            ]
        ];
      

        return $resultado;
    }
}
