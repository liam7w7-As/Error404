<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistribucionStoreRequest;
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

class DistribucionController extends Controller
{
    public function __construct(private PedidoService $pedidoService) {}

    public function index()
    {
        return Inertia::render("Admin/Distribucions/Index");
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;
        $estado = $request->input("estado", "PENDIENTES");

        $columnsSerachLike = [
            "ci",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $pedidos = $this->pedidoService->listadoPaginadoDistribucion($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy, $estado);
        return response()->JSON([
            "data" => $pedidos->items(),
            "total" => $pedidos->total(),
            "lastPage" => $pedidos->lastPage()
        ]);
    }

    public function create()
    {
        return Inertia::render("Admin/Distribucions/Create");
    }
    public function store(Pedido $pedido, DistribucionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar pedido
            $pedido = $this->pedidoService->distribucion_pedido($request->validated(), $pedido);
            DB::commit();
            return redirect()->route("distribucions.index")->with("bien", "Registro actualizado")
                ->with("url_pedido_pdf", route("pedidos.pdf", $pedido->id));
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
