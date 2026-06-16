<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "usuario" => "admin",
            "nombre" => "admin",
            "password" => "admin",
            "acceso" => 1,
            "bloqueo" => 0,
            "tipo" => "ADMINISTRADOR",
            "fecha_registro" => date("Y-m-d"),
            "status" => 1,
        ]);
    }
}
