<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    protected $fillable = [
        "distribuidor_id",
        "user_id",
        "observacion",
        "fecha",
        "hora",
        "estado",
        "comision",
    ];

    protected $appends = ["fecha_t"];

    public function getFechaTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha));
    }

    public function distribuidor()
    {
        return $this->belongsTo(User::class, 'distribuidor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function despacho_detalles()
    {
        return $this->hasMany(DespachoDetalle::class, 'despacho_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'despacho_id');
    }
}
