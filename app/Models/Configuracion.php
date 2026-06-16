<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre_sistema",
        "alias",
        "logo",
        "actividad",
        "b_hora_inicio_admin",
        "b_hora_fin_admin",
        "b_hora_inicio_dist",
        "b_hora_fin_dist",
        "b_hora_inicio_ven",
        "b_hora_fin_ven",
    ];

    protected $casts = [];

    protected $appends = ["url_logo",  "logo_b64"];
    public function getUrlLogoAttribute()
    {
        return asset("imgs/" . $this->logo);
    }
    public function getLogoB64Attribute()
    {
        $path = public_path("imgs/" . $this->logo);
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }
        return "";
    }
}
