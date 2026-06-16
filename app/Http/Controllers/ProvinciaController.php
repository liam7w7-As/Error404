<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function listado()
    {
        return response()->JSON([
            "provincias" => Provincia::all()
        ]);
    }
}
