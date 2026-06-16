<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalidaStoreRequest;
use App\Http\Requests\PedidoStoreRequest;
use App\Http\Requests\PedidoUpdateRequest;
use App\Models\Pedido;
use App\Services\PedidoService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;
use PDF;
use App\library\numero_a_letras\src\NumeroALetras;

class SalidaController extends Controller
{
    public function __construct(private PedidoService $pedidoService) {}

    public function index()
    {
        return Inertia::render("Admin/Salidas/Index");
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $distribuidor_id = (string)$request->input("distribuidor_id", "");
        $fecha_ini = (string)$request->input("fecha_ini", "");
        $fecha_fin = (string)$request->input("fecha_fin", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $pedidos = $this->pedidoService->listadoPaginadoSalida($perPage, $page, $distribuidor_id, $fecha_ini, $fecha_fin, $arrayOrderBy);
        return response()->JSON([
            "data" => $pedidos->items(),
            "total" => $pedidos->total(),
            "lastPage" => $pedidos->lastPage()
        ]);
    }

    public function create()
    {
        return Inertia::render("Admin/Salidas/Create");
    }

    public function store(SalidaStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->pedidoService->asignar_pedidos($request->validated());
            DB::commit();
            return response()->JSON([
                "sw" => true,
                "message" => "Registro realizado"
            ]);
            // return redirect()->route("salidas.create")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
    public function anular(Pedido $pedido): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->pedidoService->anularPedido($pedido);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se anulo correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
