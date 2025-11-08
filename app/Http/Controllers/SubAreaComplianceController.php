<?php

namespace App\Http\Controllers;
use App\Models\SubAreaCompliance;
use Illuminate\Http\Request;

class SubAreaComplianceController extends Controller
{
   public function findSubAreaCompliance(Request $request)
    {
       
        $subareas = SubAreaCompliance::select('id', 'area_compliance_id', 'subarea_compliance_nombre')->get();       

        return response()->json($subareas);
    }  
}
