<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoDetalle extends Model
{
    protected $fillable = [
        "despacho_id",
        "cliente_id",
        "pedido_detalle_id",
        "categoria_producto_id",
        "producto_id",
        "presentacion_producto_id",
        "cantidad",
        "cantidad_despacho",
        "cantidad_entregado",
        "cantidad_devolucion",
        "precio",
        "subtotal",
    ];

    public function despacho()
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pedido_detalle()
    {
        return $this->belongsTo(PedidoDetalle::class, 'pedido_detalle_id');
    }

    public function categoria_producto()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function presentacion_producto()
    {
        return $this->belongsTo(PresentacionProducto::class, 'presentacion_producto_id');
    }
}
