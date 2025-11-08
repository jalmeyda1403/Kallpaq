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
}
