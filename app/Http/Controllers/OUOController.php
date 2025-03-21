<?php

namespace App\Http\Controllers;
use App\Models\OUO;
use Illuminate\Http\Request;

class OUOController extends Controller
{
    public function listar()
    {

        $ouos =  OUO::all(); 
   
        return response()->json($ouos);
    }
}
