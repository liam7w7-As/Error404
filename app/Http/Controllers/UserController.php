<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\Despacho;
use App\Models\LoginUser;
use App\Models\Pedido;
use App\Models\User;
use App\Services\UserService;
use App\Services\PermisoService;
use App\Services\SegmentacionZonaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{



    protected $segmentacion_zona_ids = null;
    public function __construct(private  UserService $userService)
    {
        $this->segmentacion_zona_ids = $this->userService->getSegmentacionZona(Auth::user()->id);
    }

    public function permisosUsuario(Request $request)
    {
        $permisoService = new PermisoService();
        return response()->JSON([
            "permisos" => $permisoService->getPermisosUser()
        ]);
    }

    public function getUser()
    {
        return response()->JSON([
            "user" => Auth::user()
        ]);
    }

    public function getInfoBoxUser()
    {
        $permisos = [];
        $array_infos = [];
        if (Auth::check()) {
            $oUser = new User();
            $permisos = $oUser->permisos;
            // if ($permisos == '*' || (is_array($permisos) && in_array('usuarios.index', $permisos))) {
            //     $array_infos[] = [
            //         'label' => 'USUARIOS',
            //         'cantidad' => User::where('id', '!=', 1)->count(),
            //         'color' => 'bgWhite',
            //         'icon' => "fa-users",
            //         "url" => "usuarios.index"
            //     ];
            // }
            if ($permisos == '*' || (is_array($permisos) && in_array('clientes.index', $permisos))) {
                $clientes = Cliente::where('status', 1)->whereDate('created_at', date('Y-m-d'));
                
                if (Auth::user()->tipo == 'VENDEDOR') {
                    if (is_array($this->segmentacion_zona_ids)) {
                        $clientes->whereIn('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    } else {
                        $clientes->where('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    }
                } elseif (Auth::user()->tipo == 'DISTRIBUIDOR') {
                    $clientes->whereHas('pedidos', function($q) {
                        $q->whereHas('despacho', function($d) {
                            $d->where('distribuidor_id', Auth::user()->id);
                        })->whereIn('estado', ['PENDIENTE', 'DESPACHADO']);
                    });
                } elseif (Auth::user()->tipo != 'ADMINISTRADOR') {
                    if (is_array($this->segmentacion_zona_ids)) {
                        $clientes->whereIn('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    } else {
                        $clientes->where('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    }
                }
                
                $array_infos[] = [
                    'label' => 'CLIENTES',
                    'cantidad' => $clientes->count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-user-friends",
                    "url" => "clientes.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('pedidos.index', $permisos))) {
                $pedidos = Pedido::where('status', 1)->whereDate('created_at', date('Y-m-d'));
                
                if (Auth::user()->tipo == 'VENDEDOR') {
                    $pedidos->where('user_id', Auth::user()->id)
                            ->whereIn('estado', ['PENDIENTE', 'DESPACHADO']);
                } elseif (Auth::user()->tipo == 'DISTRIBUIDOR') {
                    $pedidos->whereHas('despacho', function($q) {
                        $q->where('distribuidor_id', Auth::user()->id);
                    })->whereIn('estado', ['PENDIENTE', 'DESPACHADO']);
                } elseif (Auth::user()->tipo != 'ADMINISTRADOR') {
                    if (is_array($this->segmentacion_zona_ids)) {
                        $pedidos->whereIn('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    } else {
                        $pedidos->where('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    }
                }

                $array_infos[] = [
                    'label' => 'PEDIDOS',
                    'cantidad' => $pedidos->count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-clipboard-list",
                    "url" => "pedidos.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('distribucions.index', $permisos))) {
                $distribucions = Pedido::where('status', 1)
                    ->whereNotNull("despacho_id")
                    ->whereIn("estado", ["DESPACHADO", "PENDIENTE"])
                    ->whereDate('created_at', date('Y-m-d'));
                
                if (Auth::user()->tipo == 'DISTRIBUIDOR') {
                    $distribucions->whereHas('despacho', function($q) {
                        $q->where('distribuidor_id', Auth::user()->id);
                    });
                } elseif (Auth::user()->tipo != 'ADMINISTRADOR') {
                    if (is_array($this->segmentacion_zona_ids)) {
                        $distribucions->whereIn('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    } else {
                        $distribucions->where('segmentacion_zona_id', $this->segmentacion_zona_ids);
                    }
                }
                $array_infos[] = [
                    'label' => 'POR ENTREGAR',
                    'cantidad' => $distribucions->count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-truck",
                    "url" => "distribucions.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('despachos.index', $permisos))) {
                $despachos = Despacho::select('despachos.*')->whereDate('created_at', date('Y-m-d'));
                if (Auth::user()->tipo == 'DISTRIBUIDOR') {
                    $despachos->where("distribuidor_id", Auth::user()->id);
                }

                $array_infos[] = [
                    'label' => 'DESPACHOS',
                    'cantidad' => $despachos->count(),
                    'color' => 'bgWhite',
                    'icon' => "fa-boxes",
                    "url" => "despachos.index"
                ];
            }
        }


        return $array_infos;
    }
}
