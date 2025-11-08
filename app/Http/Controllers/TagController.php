<?php

namespace App\Http\Controllers;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
     public function findTag(Request $request)
    {
        $query = $request->input('query');

        $tags = Tag::select('id', 'nombre')->where('nombre', 'like', '%' . $query . '%')->orderBy("id")->get();       

        return response()->json($tags);
    }  
}
