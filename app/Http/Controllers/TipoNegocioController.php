<?php

namespace App\Http\Controllers;

use App\Models\TipoNegocio;
use Illuminate\Http\Request;

class TipoNegocioController extends Controller
{
    public function listado()
    {
        return response()->JSON([
            "tipo_negocios" => TipoNegocio::all()
        ]);
    }
}
