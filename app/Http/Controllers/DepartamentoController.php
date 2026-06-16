<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function listado()
    {
        return response()->JSON([
            "departamentos" => Departamento::all()
        ]);
    }
}
