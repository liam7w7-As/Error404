<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComisionDetalle extends Model
{
    protected $fillable = [
        "comision_id",
        "despacho_id",
        "categoria_producto_id",
        "producto_id",
        "cantidad",
        "total",
        "comision_distribuidor",
        "comision_vendedor",
        "entrega_distribuidor",
        "entrega_vendedor",
        "detalle_presentacion"
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'detalle_presentacion' => 'array',
        ];
    }

    public function comision()
    {
        return $this->belongsTo(Comision::class, 'comision_id');
    }

    public function despacho()
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function categoria_producto()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
