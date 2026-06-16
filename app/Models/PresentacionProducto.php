<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresentacionProducto extends Model
{
    protected $fillable = [
        "producto_id",
        "nombre",
        "equivale",
        "precio",
        "comi_distribuidor",
        "comi_vendedor",
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function pedido_detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'presentacion_producto_id');
    }
}
