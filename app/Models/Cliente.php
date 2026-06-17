<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Cliente extends Model
{
    protected $fillable = [
        "nombre",
        "fono",
        "razon_social",
        "nit_ci",
        "dir",
        "latitud",
        "longitud",
        "tipo_negocio_id",
        "segmentacion_zona_id",
        "user_id",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function segmentacion_zona()
    {
        return $this->belongsTo(SegmentacionZona::class, 'segmentacion_zona_id');
    }

    public function scopeBuscarNombre($query, $texto)
    {
        if (!$texto) return $query;

        $palabras = explode(' ', $texto);

        foreach ($palabras as $palabra) {
            $query->where(function ($q) use ($palabra) {
                $q->where('nombre', 'like', "%$palabra%")
                    ->orWhere('razon_social', 'like', "%$palabra%");
            });
        }

        return $query;
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function tipo_negocio()
    {
        return $this->belongsTo(TipoNegocio::class, 'tipo_negocio_id');
    }

    public function ultimoPedido()
    {
        return $this->hasOne(Pedido::class)
            ->where('status', 1)
            ->latest(); // ordena por created_at DESC
    }
}
