<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $fillable = [
        "pedido_id",
        "producto_id",
        "categoria_producto_id",
        "presentacion_producto_id",
        "cantidad",
        "cantidad_total",
        "cantidad_despacho",
        "cantidad_entregado",
        "cantidad_devolucion",
        "precio",
        "subtotal",
        "status",
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function categoria_producto()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function presentacion_producto()
    {
        return $this->belongsTo(PresentacionProducto::class, 'presentacion_producto_id');
    }
}
