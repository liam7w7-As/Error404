<?php

namespace App\Services;

use App\Models\Configuracion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConfiguracionService
{
    public function __construct(private CargarArchivoService $cargarArchivoService) {}

    /**
     * Actualizar configuracion
     *
     * @param array $datos
     * @param Configuracion $configuracion
     * @return Configuracion
     */
    public function actualizar(array $datos, Configuracion $configuracion): Configuracion
    {
        $old_area = clone $configuracion;

        $configuracion = Configuracion::first();

        if (!$configuracion) {
            $configuracion = Configuracion::create([
                "nombre_sistema" => $datos["nombre_sistema"],
                "alias" => $datos["alias"],
                "actividad" => $datos["actividad"],
                "b_hora_inicio_admin" => $datos["b_hora_inicio_admin"],
                "b_hora_fin_admin" => $datos["b_hora_fin_admin"],
                "b_hora_inicio_dist" => $datos["b_hora_inicio_dist"],
                "b_hora_fin_dist" => $datos["b_hora_fin_dist"],
                "b_hora_inicio_ven" => $datos["b_hora_inicio_ven"],
                "b_hora_fin_ven" => $datos["b_hora_fin_ven"],
            ]);
        } else {
            $configuracion->update([
                "nombre_sistema" => $datos["nombre_sistema"],
                "alias" => $datos["alias"],
                "actividad" => $datos["actividad"],
                "b_hora_inicio_admin" => $datos["b_hora_inicio_admin"],
                "b_hora_fin_admin" => $datos["b_hora_fin_admin"],
                "b_hora_inicio_dist" => $datos["b_hora_inicio_dist"],
                "b_hora_fin_dist" => $datos["b_hora_fin_dist"],
                "b_hora_inicio_ven" => $datos["b_hora_inicio_ven"],
                "b_hora_fin_ven" => $datos["b_hora_fin_ven"],
            ]);
        }

        // cargar logo
        if ($datos["logo"] && !is_string($datos["logo"])) {
            $this->cargarLogo($configuracion, $datos["logo"], "logo");
        }

        return $configuracion;
    }

    public function cargarLogo(Configuracion $configuracion, UploadedFile $logo, $key): void
    {
        if ($configuracion[$key]) {
            \File::delete(public_path("imgs/" . $configuracion[$key]));
        }
        $nombre = $key . $configuracion->id . time();
        $configuracion[$key] = $this->cargarArchivoService->cargarArchivo($logo, public_path("imgs"), $nombre);
        $configuracion->save();
    }
}
