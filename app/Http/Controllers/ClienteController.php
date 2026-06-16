<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteStoreRequest;
use App\Http\Requests\ClienteUpdateRequest;
use App\Models\Cliente;
use App\Models\User;
use App\Services\ClienteService;
use App\Services\UserService;
use App\Services\PedidoService;
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

class ClienteController extends Controller
{
    public function __construct(private ClienteService $clienteService, private UserService $user_service, private PedidoService $pedidoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Clientes/Index");
    }

    /**
     * Listado de clientes
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "clientes" => $this->clienteService->listado()
        ]);
    }

    public function listado_pedido(Request $request): JsonResponse
    {
        $estado_pedido = $request->input("estado", "");
        $fecha_ini = $request->input("fecha_ini", "");
        $fecha_fin = $request->input("fecha_fin", "");

        if (Auth::user()->tipo == 'VENDEDOR') {
            return response()->JSON([
                "clientes" => $this->clienteService->listado(),
                "total_pedidos" => $this->pedidoService->totalPedidos($estado_pedido, $fecha_ini, $fecha_fin)
            ]);
        }
        return response()->JSON([
            "clientes" => $this->clienteService->listado_pedido($estado_pedido, $fecha_ini, $fecha_fin),
            "total_pedidos" => $this->pedidoService->totalPedidos($estado_pedido, $fecha_ini, $fecha_fin)
        ]);
    }

    public function listadoSegmentacion(Request $request)
    {
        $clientes = Cliente::with(["segmentacion_zona"]);
        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
        } else {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona($request->distribuidor_id);
        }

        if (!empty($segmentacion_zona_ids) && is_array($segmentacion_zona_ids)) {
            $clientes->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        $estado_pedido = $request->input("estado", "");
        if ($estado_pedido) {
            // ULTIMO PEDIDO DE CADA CLIENTE
            $clientes->whereHas("ultimoPedido", function ($query) use ($estado_pedido) {
                $query->where("estado", $estado_pedido);
            });
            if ($estado_pedido == "PENDIENTE") {
                $clientes->whereHas("ultimoPedido", function ($query) {
                    $query->whereNotNull("despacho_id");
                });

                if (Auth::user()->tipo == 'DISTRIBUIDOR') {
                    $clientes->whereHas("ultimoPedido", function ($query) {
                        $query->where("user_distribucion_id", Auth::user()->id);
                    });
                }
            }
        }
        $clientes = $clientes->get();
        return response()->JSON([
            "clientes" => $clientes
        ]);
    }

    public function byCi(Request $request)
    {
        $ci = $request->input("ci", "");
        $clientes = Cliente::where("ci", $ci)->get();
        return response()->JSON($clientes);
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

        $columnsSerachLike = [
            "nombre",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $clientes = $this->clienteService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $clientes->items(),
            "total" => $clientes->total(),
            "lastPage" => $clientes->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo cliente
     *
     * @param ClienteStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ClienteStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Cliente
            $nuevo_cliente = $this->clienteService->crear($request->validated());
            DB::commit();
            return redirect()->route("clientes.index")
                ->with("bien", "Registro realizado")
                ->with("cliente_id", $nuevo_cliente->id);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un cliente
     *
     * @param Cliente $cliente
     * @return JsonResponse
     */
    public function show(Cliente $cliente): JsonResponse
    {
        return response()->JSON($cliente);
    }

    public function update(Cliente $cliente, ClienteUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar cliente
            $this->clienteService->actualizar($request->validated(), $cliente);
            DB::commit();
            return redirect()->route("clientes.index")
                ->with("bien", "Registro actualizado")
                ->with("cliente_id", $cliente->id);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar cliente
     *
     * @param Cliente $cliente
     * @return JsonResponse|Response
     */
    public function destroy(Cliente $cliente): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->clienteService->eliminar($cliente);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function mapa_operativo(Request $request)
    {
        $fecha = $request->input("fecha", date("Y-m-d"));
        $user = Auth::user();

        $pendientes = [];
        $despachados = [];
        $entregados = [];
        $clientes_todos = [];

        $clientes_query = Cliente::select("clientes.*")->with(["segmentacion_zona", "tipo_negocio"])->where("status", 1);
        
        if ($user->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona($user->id);
            $clientes_query->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        $clientes_todos = (clone $clientes_query)->get();

        if ($user->tipo != 'VENDEDOR') {
            $query_pendientes = clone $clientes_query;
            $query_pendientes->whereHas("peddidos", function($q) use ($fecha, $user) {
                $q->whereDate("created_at", $fecha)->where("estado", "PENDIENTE")->where("status", 1);
            });
            $pendientes = $query_pendientes->get();

            $query_despachados = clone $clientes_query;
            $query_despachados->whereHas("peddidos", function($q) use ($fecha, $user) {
                $q->whereDate("created_at", $fecha)->where("estado", "DESPACHADO")->where("status", 1);
                if ($user->tipo == 'DISTRIBUIDOR') {
                    $q->where("user_distribucion_id", $user->id);
                }
            });
            $despachados = $query_despachados->get();

            $query_entregados = clone $clientes_query;
            $query_entregados->whereHas("peddidos", function($q) use ($fecha, $user) {
                $q->whereDate("created_at", $fecha)->where("estado", "ENTREGADO")->where("status", 1);
                if ($user->tipo == 'DISTRIBUIDOR') {
                    $q->where("user_distribucion_id", $user->id);
                }
            });
            $entregados = $query_entregados->get();
        }

        return response()->json([
            "pendientes" => $pendientes,
            "despachados" => $despachados,
            "entregados" => $entregados,
            "clientes_todos" => $clientes_todos
        ]);
    }
}
