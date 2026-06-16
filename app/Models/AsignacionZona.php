<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionZona extends Model
{
    protected $fillable = [
        "segmentacion_zona_id",
        "user_id",
    ];

    public function segmentacion_zona()
    {
        return $this->belongsTo(SegmentacionZona::class, 'segmentacion_zona_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
