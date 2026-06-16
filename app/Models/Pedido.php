<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        "user_id",
        "user_distribucion_id",
        "distribuidor_id",
        "segmentacion_zona_id",
        "cliente_id",
        "despacho_id",
        "consolidado_id",
        "subtotal",
        "descuento",
        "total",
        "tipo_pago",
        "fecha",
        "hora",
        "fecha_salida",
        "hora_salida",
        "observacion",
        "estado",
        "status",
    ];

    protected $appends = ["fecha_t", "fecha_salida_t", "salida"];

    public function getSalidaAttribute()
    {
        return $this->user_distribucion_id && $this->fecha_salida ? 1 : 0;
    }

    public function getFechaSalidaTAttribute()
    {
        if (!$this->fecha_salida) return "";
        return date("d/m/Y", strtotime($this->fecha_salida));
    }
    public function getFechaTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function distribuidor()
    {
        return $this->belongsTo(User::class, 'distribuidor_id');
    }

    public function user_distribucion()
    {
        return $this->belongsTo(User::class, 'user_distribucion_id');
    }

    public function segmentacion_zona()
    {
        return $this->belongsTo(SegmentacionZona::class, 'segmentacion_zona_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function despacho()
    {
        return $this->belongsTo(Despacho::class, 'despacho_id');
    }

    public function consolidado()
    {
        return $this->belongsTo(Consolidado::class, 'consolidado_id');
    }

    public function pedido_detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }

    public function pedido_detalles_venta()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id')->where("status", 1);
    }
}
