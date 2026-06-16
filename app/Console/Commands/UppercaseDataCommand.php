<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UppercaseDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:mayusculas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte la informacion antigua de la BD a mayusculas (historico)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando conversion a mayusculas...');

        // Lista de tablas y las columnas que queremos convertir a mayusculas
        $tablas = [
            'clientes' => ['nombre', 'referencia', 'tipo_negocio', 'dir'],
            'productos' => ['nombre', 'descripcion'],
            'pedidos' => ['observacion'],
            'categorias' => ['nombre', 'descripcion'],
            'proveedors' => ['razon_social', 'nombre', 'descripcion', 'contacto', 'fono', 'dir'],
            'zonas' => ['nombre', 'descripcion'],
            'users' => ['nombre', 'paterno', 'materno', 'ci', 'dir', 'tipo'],
            'cajas' => ['nombre', 'descripcion'],
            'marca_productos' => ['nombre', 'descripcion'],
            'tipo_ingresos' => ['nombre', 'descripcion'],
            'tipo_salidas' => ['nombre', 'descripcion'],
            'lotes' => ['nombre', 'descripcion', 'lote'],
        ];

        foreach ($tablas as $tabla => $columnas) {
            $this->info("Actualizando tabla: {$tabla}");
            foreach ($columnas as $columna) {
                try {
                    // Verificamos si la columna existe antes de intentar actualizarla
                    if (\Schema::hasColumn($tabla, $columna)) {
                        DB::statement("UPDATE {$tabla} SET {$columna} = UPPER({$columna}) WHERE {$columna} IS NOT NULL");
                    }
                } catch (\Exception $e) {
                    $this->error("Error en {$tabla}.{$columna}: " . $e->getMessage());
                }
            }
        }

        $this->info('Actualizacion a mayusculas completada con exito.');
    }
}
