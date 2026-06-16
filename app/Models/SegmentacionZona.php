<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentacionZona extends Model
{
    protected $fillable = [
        "departamento_id",
        "provincia_id",
        "ciudad_id",
        "zona",
        "color",
        "segmentacion",
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'segmentacion' => 'json',
        ];
    }

    public function asignacion_zonas()
    {
        return $this->hasMany(AsignacionZona::class, 'segmentacion_zona_id');
    }
}
