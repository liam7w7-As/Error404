<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enfermedad extends Model
{
    protected $fillable = [
        "nombre",
        "categoria_enfermedad_id",
        "tipo_transmision_id",
        "umbral_alerta",
        "descripcion",
    ];

    public function categoria_enfermedad()
    {
        return $this->belongsTo(CategoriaEnfermedad::class, 'categoria_enfermedad');
    }

    public function tipo_transmision()
    {
        return $this->belongsTo(TipoTransmision::class, 'tipo_transmision');
    }
}
