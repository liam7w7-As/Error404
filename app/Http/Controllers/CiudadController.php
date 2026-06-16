<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function listado()
    {
        return response()->JSON([
            "ciudads" => Ciudad::all()
        ]);
    }
}
