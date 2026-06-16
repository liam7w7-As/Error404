<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidoStoreRequest;
use App\Http\Requests\PedidoUpdateRequest;
use App\Models\CategoriaProducto;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use App\Models\Configuracion;
use App\Models\User;
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
use App\Models\PresentacionProducto;

class PedidoController extends Controller
{
    public function __construct(private PedidoService $pedidoService, private UserService $user_service) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Pedidos/Index");
    }

    /**
     * Listado de pedidos
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "pedidos" => $this->pedidoService->listado()
        ]);
    }

    public function listadoByCliente(Request $request): JsonResponse
    {
        $pedidos = Pedido::where("cliente_id", $request->cliente_id)
            ->where("estado", "PENDIENTE")
            ->whereNotNull("despacho_id")
            ->get();
        return response()->JSON([
            "pedidos" => $pedidos
        ]);
    }

    public function listadoByDistribuidor(Request $request): JsonResponse
    {
        $segmentacion_zona_ids = $this->user_service->getSegmentacionZona($request->distribuidor_id);
        $pedidos = Pedido::with(["cliente", "segmentacion_zona"])
            ->where("estado", "PENDIENTE")
            ->whereNull("user_distribucion_id")
            ->whereNull("fecha_salida")
            ->whereIn("segmentacion_zona_id", $segmentacion_zona_ids)
            ->get();
        return response()->JSON([
            "pedidos" => $pedidos
        ]);
    }

    public function pedidos_distruibidor(Request $request): JsonResponse
    {
        $segmentacion_zona_ids = $this->user_service->getSegmentacionZona($request->distribuidor_id);

        $categoria_productos = $this->pedidoService->pedido_distribuidor(null, $segmentacion_zona_ids, "PENDIENTE", true);
        return response()->JSON([
            "categoria_productos" => $categoria_productos,
        ]);
    }

    public function listado_pedidos_distribuidor(Request $request): JsonResponse
    {
        $segmentacion_zona_ids = $this->user_service->getSegmentacionZona($request->distribuidor_id);

        $pedidos = Pedido::with(["cliente.tipo_negocio", "segmentacion_zona", "user"])
            ->where("status", 1)
            ->where("estado", "PENDIENTE");

        if (is_array($segmentacion_zona_ids)) {
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        } else {
            $pedidos->where("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        return response()->JSON([
            "pedidos" => $pedidos->get(),
        ]);
    }

    public function pedidos_despacho(Request $request): JsonResponse
    {
        // $segmentacion_zona = $this->user_service->getSegmentacionZona($request->distribuidor_id);

        $categoria_productos = $this->pedidoService->pedidos_despacho(null, null, $request->estado, (bool)$request->detalles);

        return response()->JSON([
            "categoria_productos" => $categoria_productos,
        ]);
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $fecha_ini = (string)$request->input("fecha_ini", "");
        $fecha_fin = (string)$request->input("fecha_fin", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

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

        $pedidos = $this->pedidoService->listadoPaginado($perPage, $page, $fecha_ini, $fecha_fin, $arrayOrderBy);

        // Indicadores de resumen (pendientes en el rango de fechas)
        $total_pendientes = $this->pedidoService->totalPedidosCount("PENDIENTE", $fecha_ini, $fecha_fin);
        $total_bs_pendientes = $this->pedidoService->totalPedidos("PENDIENTE", $fecha_ini, $fecha_fin);

        return response()->JSON([
            "data" => $pedidos->items(),
            "total" => $pedidos->total(),
            "lastPage" => $pedidos->lastPage(),
            "total_pendientes" => $total_pendientes,
            "total_bs_pendientes" => $total_bs_pendientes,
        ]);
    }
    /**
     * Página create
     *
     * @return Response
     */
    public function create(): ResponseInertia
    {
        return Inertia::render("Admin/Pedidos/Create");
    }
    /**
     * Registrar un nuevo pedido
     *
     * @param PedidoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(PedidoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Pedido
            $pedido = $this->pedidoService->crear($request->validated());
            DB::commit();
            return redirect()->route("pedidos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un pedido
     *
     * @param Pedido $pedido
     * @return JsonResponse
     */
    public function show(Pedido $pedido): JsonResponse
    {
        return response()->JSON($pedido->load(["pedido_detalles.producto", "pedido_detalles.presentacion_producto"]));
    }
    /**
     * Página edit
     *
     * @return Response
     */
    public function edit(Pedido $pedido): ResponseInertia
    {
        $pedido = $pedido->load(["pedido_detalles.producto", "pedido_detalles.presentacion_producto"]);
        return Inertia::render("Admin/Pedidos/Edit", compact("pedido"));
    }
    /**
     * Página ver
     *
     * @return Response
     */
    public function ver(Pedido $pedido): ResponseInertia
    {
        $pedido = $pedido->load(["pedido_detalles.producto", "pedido_detalles.presentacion_producto", "cliente.segmentacion_zona"]);
        return Inertia::render("Admin/Pedidos/Ver", compact("pedido"));
    }

    public function pdf(Pedido $pedido)
    {
        $pedido = $pedido->load([
            "pedido_detalles.producto",
            "pedido_detalles.presentacion_producto",
            "cliente",
            "user_distribucion",
        ]);

        $configuracion = Configuracion::get()->first();
        // CANTIDAD DE ITEMS
        $cantidadItems = $pedido->pedido_detalles->count();
        $baseHeight = 260; // cabecera + totales
        $itemHeight = 75; // espacio por item

        $alto = $baseHeight + ($cantidadItems * $itemHeight);

        /*
        |--------------------------------------------------------------------------
        | TAMAÑO PAPEL TÉRMICO 80mm
        |--------------------------------------------------------------------------
        */

        $customPaper = [0, 0, 226.77, $alto];


        $convertir = new NumeroALetras();
        $array_monto = explode('.', number_format($pedido->total, 2, '.', ''));
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
        // primero todo a minúsculas
        $literal = strtolower($literal);
        $literal = ucfirst($literal);
        $pedido->literal = $literal;
        $pedidos = [$pedido];

        $pdf = PDF::loadView(
            'reportes.pedido_termico',
            compact('pedidos', 'configuracion')
        )->setPaper($customPaper);

        return $pdf->stream('pedido_termico.pdf');
    }

    public function pdf_pendientes()
    {
        $configuracion = Configuracion::get()->first();
        $convertir = new NumeroALetras();

        $pedidos = Pedido::with([
            "pedido_detalles.producto",
            "pedido_detalles.presentacion_producto",
            "cliente",
            "user_distribucion",
        ])
            ->where("estado", "PENDIENTE");

        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        $cantidadItems = 0;
        $pedidos = $pedidos->where("status", 1)
            ->get()->map(function ($pedido) use ($convertir, &$cantidadItems) {
                $array_monto = explode('.', number_format($pedido->total, 2, '.', ''));
                $literal = $convertir->convertir($array_monto[0]);
                $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
                // primero todo a minúsculas
                $literal = strtolower($literal);
                $literal = ucfirst($literal);
                $pedido->literal = $literal;

                // CANTIDAD DE ITEMS
                $cantidadItems += $pedido->pedido_detalles->count();
                return $pedido;
            });

        /*
        |--------------------------------------------------------------------------
        | TAMAÑO PAPEL TÉRMICO 80mm
        |--------------------------------------------------------------------------
        */
        $baseHeight = 260; // cabecera + totales
        $itemHeight = 30; // espacio por item
        $alto = $baseHeight + ($cantidadItems * $itemHeight);
        $customPaper = [0, 0, 226.77, $alto];

        $pdf = PDF::loadView(
            'reportes.pedido_termico',
            compact('pedidos', 'configuracion')
        )->setPaper($customPaper);

        return $pdf->stream('pedido_termico.pdf');
    }
    public function pdf_preparacion_general(Request $request)
    {
        $fecha = date('Y-m-d');
        $categoria_productos = $this->pedidoService->pedido_preparacion_general($fecha);
        $pdf = PDF::loadView('reportes.despacho', compact('categoria_productos'))->setPaper('legal', 'landscape');
        return $pdf->stream('preparacion_general.pdf');
    }

    public function pdf_notas_seleccionadas(Request $request)
    {
        $ids = explode(',', $request->ids);
        $configuracion = Configuracion::get()->first();
        $convertir = new NumeroALetras();

        $pedidos = Pedido::with([
            "pedido_detalles.producto",
            "pedido_detalles.presentacion_producto",
            "cliente",
            "user_distribucion",
        ])->whereIn("id", $ids)->where("status", 1)->get();

        $cantidadItems = 0;
        $pedidos = $pedidos->map(function ($pedido) use ($convertir, &$cantidadItems) {
            $array_monto = explode('.', number_format($pedido->total, 2, '.', ''));
            $literal = $convertir->convertir($array_monto[0]);
            $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";
            $literal = strtolower($literal);
            $literal = ucfirst($literal);
            $pedido->literal = $literal;

            // CANTIDAD DE ITEMS
            $cantidadItems += $pedido->pedido_detalles->count();
            return $pedido;
        });

        $baseHeight = 260; 
        $itemHeight = 30; 
        $alto = $baseHeight + ($cantidadItems * $itemHeight);
        $customPaper = [0, 0, 226.77, $alto];

        $pdf = PDF::loadView(
            'reportes.pedido_termico',
            compact('pedidos', 'configuracion')
        )->setPaper($customPaper);

        return $pdf->stream('pedido_termico.pdf');
    }

    public function update(Pedido $pedido, PedidoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar pedido
            $pedido = $this->pedidoService->actualizar($request->validated(), $pedido);
            DB::commit();
            return redirect()->route("pedidos.index")->with("bien", "Registro actualizado");
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
            if ($pedido->estado == 'DESPACHADO' && Auth::user()->tipo != 'ADMINISTRADOR') {
                throw new \Exception('No tiene permisos para anular un pedido que ya ha sido despachado.');
            }
            if ($pedido->estado == 'ENTREGADO') {
                throw new \Exception('No puede anular un pedido entregado.');
            }

            $this->pedidoService->anularPedido($pedido);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El pedido se anuló correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar pedido
     *
     * @param Pedido $pedido
     * @return JsonResponse|Response
     */
    public function destroy(Pedido $pedido): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            if ($pedido->estado == 'DESPACHADO' && Auth::user()->tipo != 'ADMINISTRADOR') {
                throw new \Exception('No tiene permisos para eliminar un pedido que ya ha sido despachado.');
            }

            $this->pedidoService->eliminar($pedido);
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
}
