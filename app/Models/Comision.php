<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    protected $fillable = [
        "distribuidor_id",
        "user_id",
        "fecha",
        "hora",
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

    public function comision_detalles()
    {
        return $this->hasMany(ComisionDetalle::class);
    }
}
