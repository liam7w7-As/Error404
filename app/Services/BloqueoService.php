<?php

namespace App\Services;

use App\Models\Configuracion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class BloqueoService
{
    public function verificaBloqueoUsuario()
    {
        $configuracion = Configuracion::first();

        if (Auth::user()->bloqueo == 1) {
            // Horario por defecto
            $hora_inicio = "08:00";
            $hora_fin = "18:00";

            if ($configuracion) {
                if (Auth::user()->tipo == 'ADMINISTRADOR') {
                    $hora_inicio = $configuracion->b_hora_inicio_admin;
                    $hora_fin = $configuracion->b_hora_fin_admin;
                }

                if (Auth::user()->tipo == 'DISTRIBUIDOR') {
                    $hora_inicio = $configuracion->b_hora_inicio_dist;
                    $hora_fin = $configuracion->b_hora_fin_dist;
                }

                if (Auth::user()->tipo == 'VENDEDOR') {
                    $hora_inicio = $configuracion->b_hora_inicio_ven;
                    $hora_fin = $configuracion->b_hora_fin_ven;
                }
            }

            // Solo hora actual
            $hora_actual = Carbon::now()->format('H:i');

            if ($hora_actual < $hora_inicio || $hora_actual > $hora_fin) {
                return true;
            }
        }


        return false;
    }
}
