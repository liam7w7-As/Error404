<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuracion::create([
            "nombre_sistema" => "NAVI",
            "alias" => "NAVI",
            "logo" => "logo.jpg",
            "actividad" => "ACTIVIDAD NAVI",
            "b_hora_inicio_admin" => "08:00:00",
            "b_hora_fin_admin" => "18:00:00",
            "b_hora_inicio_dist" => "08:00:00",
            "b_hora_fin_dist" => "18:00:00",
            "b_hora_inicio_ven" => "08:00:00",
            "b_hora_fin_ven" => "18:00:00",
        ]);
    }
}
