<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $fillable = [
        "nombre"
    ];



    public function pedido_detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'categoria_producto_id');
    }
}
