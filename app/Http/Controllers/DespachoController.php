<?php

namespace App\Http\Controllers;

use App\Http\Requests\DespachoStoreRequest;
use App\Models\Despacho;
use App\Models\CategoriaProducto;
use App\Models\PedidoDetalle;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Services\DespachoService;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use PDF;

class DespachoController extends Controller
{
    public function __construct(private DespachoService $despachoService, private PedidoService $pedidoService) {}
    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Despachos/Index");
    }

    public function listado(Request $request): JsonResponse
    {
        $despachos = Despacho::select("despachos.*");
        if (isset($request->estado)) {
            $despachos = $despachos->where("estado", $request->estado);
        }
        if (isset($request->comision)) {
            $despachos = $despachos->where("comision", $request->comision);
        }
        if (isset($request->distribuidor_id)) {
            $despachos = $despachos->where("distribuidor_id", $request->distribuidor_id);
        }
        $despachos = $despachos->get();

        return response()->JSON([
            "despachos" => $despachos
        ]);
    }

    public function ver(Despacho $despacho): ResponseInertia
    {
        $despacho = $despacho->load(["distribuidor"]);
        $categoria_productos = $this->pedidoService->pedido_distribuidor($despacho->id);

        return Inertia::render("Admin/Despachos/Ver", compact("despacho", "categoria_productos"));
    }

    public function pdf(Despacho $despacho)
    {
        $despacho = $despacho->load(["distribuidor"]);
        $categoria_productos = $this->pedidoService->pedido_distribuidor($despacho->id);

        $pdf = PDF::loadView('reportes.despacho', compact('despacho', 'categoria_productos'))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('despacho.pdf');
    }

    public function pedidos_despacho_comision(Request $request): JsonResponse
    {
        $distribuidor_id = $request->distribuidor_id;
        $estado_despacho = "CONSOLIDADO";
        $despachos = Despacho::where("distribuidor_id", $distribuidor_id)
            ->where("comision", 0)
            ->where("estado", "CONSOLIDADO")
            ->orderBy("id", "asc")
            ->get()
            ->map(function ($despacho) use ($estado_despacho, $distribuidor_id) {
                $despacho_id = $despacho->id;
                $despacho->categoria_productos = CategoriaProducto::whereHas("pedido_detalles", function ($q) use ($despacho_id, $estado_despacho) {
                    $q->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho) {
                        $sub->where("despacho_id", $despacho_id);
                        $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                            $sub2->where("estado", $estado_despacho);
                        });
                        $sub->where("status", 1);
                    });
                })->distinct()
                    ->orderBy("nombre", "asc")->get()
                    ->map(function ($categoria) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                        $categoria->productos = Producto::whereHas("pedido_detalles", function ($q) use ($categoria,  $despacho_id, $estado_despacho, $distribuidor_id) {
                            $q->where("categoria_producto_id", $categoria->id);
                            $q->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                $sub->where("despacho_id", $despacho_id);
                                $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                                    $sub2->where("estado", $estado_despacho);
                                });
                                $sub->where("status", 1);
                            });
                        })->orderBy("nombre", "asc")
                            ->get()
                            ->map(function ($producto) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                $producto->ver = false;

                                $producto->cantidad_entregado = PedidoDetalle::where("producto_id", $producto->id)
                                    ->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                        $sub->where("despacho_id", $despacho_id);
                                        $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                                            $sub2->where("estado", $estado_despacho);
                                        });
                                        $sub->where("status", 1);
                                    })->sum("cantidad_entregado");

                                $producto->monto_vendido = PedidoDetalle::where("producto_id", $producto->id)
                                    ->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                        $sub->where("despacho_id", $despacho_id);
                                        $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                                            $sub2->where("estado", $estado_despacho);
                                        });
                                        $sub->where("status", 1);
                                    })->sum("subtotal");

                                $producto->comision_distribuidor = 0;
                                $producto->comision_vendedor = 0;

                                $total_comision_distribuidor = 0;
                                $total_comision_vendedor = 0;

                                $producto->presentacions = PresentacionProducto::whereHas("pedido_detalles", function ($q) use ($producto,  $despacho_id, $estado_despacho, $distribuidor_id) {
                                    $q->where("producto_id", $producto->id);
                                    $q->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                        $sub->where("despacho_id", $despacho_id);
                                        $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                                            $sub2->where("estado", $estado_despacho);
                                        });
                                        $sub->where("status", 1);
                                    });
                                })->orderBy("nombre", "asc")
                                    ->get()
                                    ->map(function ($presentacion) use (
                                        $producto,
                                        $despacho_id,
                                        $estado_despacho,
                                        $distribuidor_id,
                                        &$total_comision_distribuidor,
                                        &$total_comision_vendedor
                                    ) {
                                        $presentacion->ver = false;
                                        $presentacion->total_cantidad = PedidoDetalle::where("producto_id", $producto->id)
                                            ->where("presentacion_producto_id", $presentacion->id)
                                            ->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                                $sub->where("despacho_id", $despacho_id);
                                                $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                                                    $sub2->where("estado", $estado_despacho);
                                                });
                                                $sub->where("status", 1);
                                            })->sum("cantidad");
                                        $presentacion->cantidad_presentacion = PedidoDetalle::where("producto_id", $producto->id)
                                            ->where("presentacion_producto_id", $presentacion->id)
                                            ->whereHas("pedido", function ($sub) use ($despacho_id, $estado_despacho, $distribuidor_id) {
                                                $sub->where("despacho_id", $despacho_id);
                                                $sub->whereHas("despacho", function ($sub2) use ($estado_despacho) {
                                                    $sub2->where("estado", $estado_despacho);
                                                });
                                                $sub->where("status", 1);
                                            })->sum("cantidad");

                                        $presentacion->total = round($presentacion->cantidad_presentacion * $presentacion->precio, 2);
                                        $presentacion->comision_distribuidor = round(($presentacion->cantidad_presentacion * $presentacion->precio) * ($presentacion->comi_distribuidor / 100), 2);
                                        $presentacion->comision_vendedor = round(($presentacion->cantidad_presentacion * $presentacion->precio) * ($presentacion->comi_vendedor / 100), 2);
                                        // porcentajes
                                        $presentacion->p_distribuidor = $presentacion->comi_distribuidor;
                                        $presentacion->p_vendedor = $presentacion->comi_vendedor;
                                        // Acumular
                                        $total_comision_distribuidor += $presentacion->comision_distribuidor;
                                        $total_comision_vendedor += $presentacion->comision_vendedor;
                                        return $presentacion;
                                    });
                                // Asignar totales al producto
                                $producto->comision_distribuidor = round($total_comision_distribuidor, 2);
                                $producto->comision_vendedor = round($total_comision_vendedor, 2);

                                $producto->entrega_distribuidor = round($total_comision_distribuidor, 2);
                                $producto->entrega_vendedor = round($total_comision_vendedor, 2);
                                return $producto;
                            });
                        return $categoria;
                    });
                return $despacho;
            });

        return response()->JSON([
            "despachos" => $despachos
        ]);
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $producto_id = (string)$request->input("producto_id", "");
        $cliente_id = (string)$request->input("cliente_id", "");
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

        $clientes = $this->despachoService->listadoPaginado($perPage, $page, $search, $producto_id, $cliente_id, $distribuidor_id, $fecha_ini, $fecha_fin, $arrayOrderBy);
        return response()->JSON([
            "data" => $clientes->items(),
            "total" => $clientes->total(),
            "lastPage" => $clientes->lastPage()
        ]);
    }

    /**
     * Página create
     *
     * @return Response
     */
    public function create(): ResponseInertia
    {
        return Inertia::render("Admin/Despachos/Create");
    }

    /**
     * Registrar un nuevo despacho
     *
     * @param DespachoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(DespachoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Despacho
            $this->despachoService->crear($request->validated());
            DB::commit();
            return redirect()->route("despachos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function pdf_seleccionados(Request $request)
    {
        $ids = explode(',', $request->ids);
        $categoria_productos = $this->pedidoService->pedido_seleccionados($ids);
        $pdf = PDF::loadView('reportes.despacho', compact('categoria_productos'))->setPaper('legal', 'landscape');
        return $pdf->stream('despacho_seleccionados.pdf');
    }

    public function pdf_seleccionados_cliente(Request $request)
    {
        $clientes = $this->pedidoService->pedido_seleccionados_cliente($request->all());
        $fecha_ini = $request->fecha_ini ?? date('Y-m-d');
        $fecha_fin = $request->fecha_fin ?? date('Y-m-d');
        $pdf = PDF::loadView('reportes.despacho_cliente', compact('clientes', 'fecha_ini', 'fecha_fin'))->setPaper('legal', 'landscape');
        return $pdf->stream('despacho_seleccionados_cliente.pdf');
    }
}
