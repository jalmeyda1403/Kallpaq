<?php

namespace App\Http\Controllers;
use App\Models\AreaCompliance;
use Illuminate\Http\Request;

class AreaComplianceController extends Controller
{
    public function findAreaCompliance(Request $request)
    {

        $areas = AreaCompliance::select('id', 'area_compliance_nombre')->get();

        return response()->json($areas);
    }

    public function apiList()
    {
        $areas = AreaCompliance::select('id', 'area_compliance_nombre')
            ->orderBy('area_compliance_nombre')
            ->get();
        return response()->json($areas);
    }

    public function apiSubareas($areaId)
    {
        $area = AreaCompliance::findOrFail($areaId);
        $subareas = $area->subareas()
            ->select('id', 'subarea_compliance_nombre')
            ->orderBy('subarea_compliance_nombre')
            ->get();
        return response()->json($subareas);
    }
}
