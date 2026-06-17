<?php

namespace App\Services;

use App\Models\CategoriaProducto;
use App\Models\Cliente;
use App\Services\HistorialAccionService;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use App\Models\PresentacionProducto;
use App\Models\User;
use App\Models\Despacho;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PedidoService
{
    private $modulo = "PEDIDOS";

    public function __construct(
        private  CargarArchivoService $cargarArchivoService,
        private HistorialAccionService $historialAccionService,
        private MovimientoInventarioService $movimiento_inventario_service,
        private UserService $user_service,
        private ProductoService $producto_service,
        private AsignacionZonaService $asignacion_zona_service
    ) {}

    public function listado(): Collection
    {
        $pedidos = Pedido::select("pedidos.*")
            ->where("status", 1)->get();
        return $pedidos;
    }

    public function totalPedidos($estado_pedido, $fecha_ini, $fecha_fin)
    {
        $pedidos = Pedido::select("pedidos.*")
            ->where("status", 1);

        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        if ($estado_pedido) {
            $pedidos->where("estado", $estado_pedido);
        }

        if ($fecha_ini && $fecha_fin) {
            $pedidos->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
        }

        if (Auth::user()->tipo == 'DISTRIBUIDOR') {
            $pedidos->where("user_distribucion_id", Auth::user()->id);
        }

        return $pedidos->sum("total");
    }

    public function totalPedidosCount($estado_pedido, $fecha_ini, $fecha_fin)
    {
        $pedidos = Pedido::select("pedidos.*")
            ->where("status", 1);

        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        if ($estado_pedido) {
            $pedidos->where("estado", $estado_pedido);
        }

        if ($fecha_ini && $fecha_fin) {
            $pedidos->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
        }

        if (Auth::user()->tipo == 'DISTRIBUIDOR') {
            $pedidos->where("user_distribucion_id", Auth::user()->id);
        }

        return $pedidos->count();
    }

    /**
     * Lista de pedidos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $fecha_ini, $fecha_fin, array $orderBy = []): LengthAwarePaginator
    {
        $pedidos = Pedido::select("pedidos.*")
            ->with(["cliente.tipo_negocio", "segmentacion_zona:id,zona"])
            ->where("status", 1);


        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }


        if ($fecha_ini && $fecha_fin) {
            $pedidos->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $pedidos->orderBy($value[0], $value[1]);
            }
        }


        $pedidos = $pedidos->paginate($length, ['*'], 'page', $page);
        return $pedidos;
    }

    public function listadoPaginadoDistribucion(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = [], string $estado = 'PENDIENTES'): LengthAwarePaginator
    {
        $pedidos = Pedido::select("pedidos.*")
            ->with([
                "cliente:id,nombre,razon_social,fono,tipo_negocio_id",
                "cliente.tipo_negocio",
                "user",
                "segmentacion_zona:id,zona"
            ])
            ->where("status", 1)
            ->whereNotNull("despacho_id");

        if ($estado == 'ENTREGADOS') {
            $pedidos->where("estado", "ENTREGADO");
        } else {
            $pedidos->whereIn("estado", ["DESPACHADO", "PENDIENTE"]);
        }

        if (Auth::user()->tipo == 'DISTRIBUIDOR') {
            $pedidos->whereHas('despacho', function($q) {
                $q->where('distribuidor_id', Auth::user()->id);
            });
        } elseif (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $pedidos->where("pedidos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $pedidos->whereBetween("pedidos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $pedidos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $pedidos->orderBy($value[0], $value[1]);
            }
        }


        $pedidos = $pedidos->paginate($length, ['*'], 'page', $page);
        return $pedidos;
    }

    public function listadoPaginadoSalida(int $length, int $page, string $distribuidor_id, $fecha_ini, $fecha_fin, $orderBy = []): LengthAwarePaginator
    {
        $pedidos = Pedido::select("pedidos.*")
            ->with([
                "cliente:id,nombre,razon_social",
                "cliente.tipo_negocio",
                "user_distribucion",
                "segmentacion_zona:id,zona"
            ])
            ->where("status", 1)
            ->whereNotNull("user_distribucion_id");
        // ->where("estado", "ENTREGADO");

        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $pedidos->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
        }


        if ($distribuidor_id) {
            $pedidos->where("user_distribucion_id", $distribuidor_id);
        }

        if ($fecha_ini && $fecha_fin) {
            $pedidos->whereBetween("fecha_salida", [$fecha_ini, $fecha_fin]);
        }


        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $pedidos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $pedidos->orderBy($value[0], $value[1]);
            }
        }


        $pedidos = $pedidos->paginate($length, ['*'], 'page', $page);
        return $pedidos;
    }

    /**
     * Crear pedido
     *
     * @param array $datos
     * @return Pedido
     */
    public function crear(array $datos): Pedido
    {

        $fecha_actual = Carbon::now("America/La_Paz")->format("Y-m-d");
        $hora_actual = Carbon::now("America/La_Paz")->format("H:i:s");

        // if (Auth::user()->tipo != 'ADMINISTRADOR') {
        //     $segmentacion_zona_id =  $this->user_service->getSegmentacionZona(Auth::user()->id)->id;
        // } else {
        //     $segmentacion_zona_id =  $this->user_service->getSegmentacionZona($datos["distribuidor_id"])->id;
        // }

        $cliente = Cliente::findOrFail($datos["cliente_id"]);

        $distribuidor = $this->asignacion_zona_service->getDistribuidorPorSegmentacionZona($cliente->segmentacion_zona_id);
        if (!$distribuidor) {
            throw ValidationException::withMessages(['No se encontró un distribuidor en la ubicación del Cliente.']);
        }

        $pedido = Pedido::create([
            "distribuidor_id" => $distribuidor ? $distribuidor->id : null,
            "cliente_id" => $datos["cliente_id"],
            "subtotal" => $datos["subtotal"],
            "descuento" => $datos["descuento"],
            "total" => $datos["total"],
            "observacion" => $datos["observacion"],
            "user_id" => Auth::user()->id,
            "segmentacion_zona_id" => $cliente->segmentacion_zona_id,
            "fecha" => $fecha_actual,
            "hora" => $hora_actual,
        ]);

        // DETALLES
        foreach ($datos["pedido_detalles"] as $item) {
            $producto = Producto::lockForUpdate()->findOrFail($item["producto_id"]);

            // validar cantidad disponible
            $arr_disponible = $this->producto_service->verificaStockDisponible($producto->id, $item["cantidad_total"]);
            if (!$arr_disponible[0]) {
                throw new Exception("La cantidad disponible del producto " . $producto->nombre . " es de " . $arr_disponible[1] . "; insuficiente para lo solicitado de " . $item['cantidad_total']);
            }

            $datos_detalle = [
                "producto_id" => $item["producto_id"],
                "categoria_producto_id" => $producto->categoria_producto_id,
                "presentacion_producto_id" => $item["presentacion_producto_id"],
                "cantidad" => $item["cantidad"],
                "cantidad_total" => $item["cantidad_total"],
                "cantidad_despacho" => 0,
                "cantidad_entregado" => 0,
                "cantidad_devolucion" => 0,
                "precio" => $item["precio"],
                "subtotal" => $item["subtotal"],
            ];

            $pedido->pedido_detalles()->create($datos_detalle);

            // Reservar stock
            $producto->stock_reservado += (float)$item["cantidad_total"];
            $producto->save();
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PEDIDO", $pedido, null, ["pedido_detalles"]);

        return $pedido;
    }

    /**
     * Actualizar pedido
     *
     * @param array $datos
     * @param Pedido $pedido
     * @return Pedido
     */
    public function actualizar(array $datos, Pedido $pedido): Pedido
    {
        $old_pedido = clone $pedido;

        // if (Auth::user()->tipo == 'ADMINISTRADOR') {
        //     $segmentacion_zona_id =  $this->user_service->getSegmentacionZona($datos["distribuidor_id"])->id;
        // }

        $cliente = Cliente::findOrFail($datos["cliente_id"]);

        $distribuidor = $this->asignacion_zona_service->getDistribuidorPorSegmentacionZona($cliente->segmentacion_zona_id);
        if (!$distribuidor) {
            throw ValidationException::withMessages(['No se encontró un distribuidor en la ubicación del Cliente.']);
        }

        $pedido->update([
            "distribuidor_id" => $distribuidor ? $distribuidor->id : null,
            "cliente_id" => $datos["cliente_id"],
            "subtotal" => $datos["subtotal"],
            "descuento" => $datos["descuento"],
            "total" => $datos["total"],
            "segmentacion_zona_id" => $cliente->segmentacion_zona_id,
            "observacion" => $datos["observacion"],
        ]);

        // DETALLES (Solo si está pendiente)
        if ($old_pedido->estado == 'PENDIENTE') {
            foreach ($datos["pedido_detalles"] as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item["producto_id"]);

                // validar cantidad disponible
                $arr_disponible = $this->producto_service->verificaStockDisponible($producto->id, $item["cantidad_total"], $pedido->id);
                if (!$arr_disponible[0]) {
                    throw new Exception("La cantidad disponible del producto " . $producto->nombre . " es de " . $arr_disponible[1] . "; insuficiente para lo solicitado de " . $item['cantidad_total']);
                }

                $datos_detalle = [
                    "producto_id" => $item["producto_id"],
                    "categoria_producto_id" => $producto->categoria_producto_id,
                    "presentacion_producto_id" => $item["presentacion_producto_id"],
                    "cantidad" => $item["cantidad"],
                    "cantidad_total" => $item["cantidad_total"],
                    "cantidad_despacho" => 0,
                    "cantidad_entregado" => 0,
                    "cantidad_devolucion" => 0,
                    "precio" => $item["precio"],
                    "subtotal" => $item["subtotal"],
                ];
                if ($item["id"] == 0) {
                    $pedido->pedido_detalles()->create($datos_detalle);
                    // Reservar stock nuevo
                    $producto->stock_reservado += (float)$item["cantidad_total"];
                    $producto->save();
                } else {
                    $pedido_detalle = PedidoDetalle::find($item["id"]);
                    
                    // Ajustar reserva si la cantidad cambió
                    $diff = (float)$item["cantidad_total"] - (float)$pedido_detalle->cantidad_total;
                    if ($diff != 0) {
                        $producto->stock_reservado += $diff;
                        $producto->save();
                    }

                    $pedido_detalle->update($datos_detalle);
                }
            }

            if (isset($datos["eliminados"])) {
                foreach ($datos["eliminados"] as $value) {
                    $pedido_detalle = PedidoDetalle::find($value);
                    
                    // Liberar reserva si aún no se ha despachado completamente
                    $producto = Producto::lockForUpdate()->find($pedido_detalle->producto_id);
                    $cantidad_pendiente = (float)$pedido_detalle->cantidad_total - (float)$pedido_detalle->cantidad_despacho;
                    if ($cantidad_pendiente > 0) {
                        $producto->stock_reservado -= $cantidad_pendiente;
                        if ($producto->stock_reservado < 0) $producto->stock_reservado = 0;
                        $producto->save();
                    }

                    $pedido_detalle->delete();
                }
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PEDIDO", $old_pedido, $pedido->withoutRelations(), ["pedido_detalles"]);

        return $pedido;
    }

    public function distribucion_pedido(array $datos, Pedido $pedido): Pedido
    {
        $old_pedido = clone $pedido;

        $despacho = Despacho::findOrFail($pedido->despacho_id);

        $pedido->update([
            "distribuidor_id" => $despacho->distribuidor_id,
            // "user_distribucion_id" => Auth::user()->id,
            "subtotal" => $datos["subtotal"],
            "descuento" => $datos["descuento"],
            "total" => $datos["total"],
            "observacion" => $datos["observacion"],
            "tipo_pago" => $datos["tipo_pago"],
            "estado" => "ENTREGADO"
        ]);

        // DETALLES
        foreach ($datos["pedido_detalles"] as $item) {
            $producto = Producto::findOrFail($item["producto_id"]);
            $datos_detalle = [
                "producto_id" => $item["producto_id"],
                "categoria_producto_id" => $producto->categoria_producto_id,
                "presentacion_producto_id" => $item["presentacion_producto_id"],
                "cantidad" => $item["cantidad"],
                "cantidad_entregado" => $item["cantidad_entregado"],
                "cantidad_devolucion" => (float)$item["cantidad_despacho"] - (float)$item["cantidad_entregado"],
                "precio" => $item["precio"],
                "subtotal" => $item["subtotal"],
            ];
            $pedido_detalle = PedidoDetalle::find($item["id"]);
            $pedido_detalle->update($datos_detalle);
        }

        if (isset($datos["eliminados"])) {
            foreach ($datos["eliminados"] as $value) {
                $pedido_detalle = PedidoDetalle::find($value);
                $pedido_detalle->cantidad_devolucion = $pedido_detalle->cantidad_despacho;
                $pedido_detalle->status = 2; // ELIMINADO POR DISTRIBUCIÓN
                $pedido_detalle->save();
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ENTREGÓ UN PEDIDO", $old_pedido, $pedido->withoutRelations(), ["pedido_detalles"]);

        return $pedido;
    }


    /**
     * Eliminar pedido
     *
     * @param Pedido $pedido
     * @return boolean
     */
    public function eliminar(Pedido $pedido): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_pedido = clone $pedido;
        
        if ($pedido->estado == 'PENDIENTE' || $pedido->estado == 'DESPACHADO') {
            foreach ($pedido->pedido_detalles as $item) {
                $producto = Producto::lockForUpdate()->find($item->producto_id);
                if ($producto) {
                    // Revertir despacho físico si ya estaba despachado
                    if ($pedido->estado == 'DESPACHADO' && $item->cantidad_despacho > 0) {
                        $presentacionProducto = \App\Models\PresentacionProducto::find($item->presentacion_producto_id);
                        $this->movimiento_inventario_service->registrarMovimiento("PedidoDetalle", "INGRESO", $producto, $item->cantidad_despacho, $presentacionProducto->precio, "Por anulación de pedido despachado", "PedidoDetalle", $item->id);
                    }

                    $cantidad_pendiente = (float)$item->cantidad_total - (float)$item->cantidad_despacho;
                    if ($cantidad_pendiente > 0) {
                        $producto->stock_reservado -= $cantidad_pendiente;
                        if ($producto->stock_reservado < 0) $producto->stock_reservado = 0;
                        $producto->save();
                    }
                }
            }
        }

        $pedido->status = 0;
        $pedido->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PEDIDO", $old_pedido, $pedido);

        return true;
    }

    public function anularPedido(Pedido $pedido): Pedido
    {
        $old_pedido = clone $pedido;
        $pedido->status = 0;
        $pedido->estado = "ANULADO";
        $pedido->save();

        // DETALLES
        foreach ($pedido->pedido_detalles as $item) {
            $producto = Producto::lockForUpdate()->findOrFail($item->producto_id);
            $pedido_detalle = PedidoDetalle::find($item->id);
            $presentacionProducto = PresentacionProducto::findOrFail($pedido_detalle->presentacion_producto_id);
            
            if ($pedido_detalle->cantidad_despacho > 0) {
                $this->movimiento_inventario_service->registrarMovimiento("PedidoDetalle", "INGRESO", $producto, $pedido_detalle->cantidad_despacho, $presentacionProducto->precio, "Por anulación de pedido", "PedidoDetalle", $pedido_detalle->id);
            }

            // Liberar stock reservado pendiente
            $cantidad_pendiente = (float)$pedido_detalle->cantidad_total - (float)$pedido_detalle->cantidad_despacho;
            if ($cantidad_pendiente > 0) {
                $producto->stock_reservado -= $cantidad_pendiente;
                if ($producto->stock_reservado < 0) $producto->stock_reservado = 0;
                $producto->save();
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ANULÓ UN PEDIDO", $old_pedido, $pedido);

        return $pedido;
    }

    public function pedido_distribuidor($despacho_id = null, $segmentacion_zona_ids = null, $estado = null, $valida_despacho = false)
    {
        return CategoriaProducto::whereHas("pedido_detalles", function ($q) use ($segmentacion_zona_ids, $estado, $despacho_id, $valida_despacho) {
            $q->whereHas("pedido", function ($sub) use ($segmentacion_zona_ids, $estado, $despacho_id, $valida_despacho) {
                if ($despacho_id) {
                    $sub->where("despacho_id", $despacho_id);
                }
                if ($segmentacion_zona_ids !== null) {
                    $sub->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
                }
                if ($estado) {
                    $sub->where("estado", $estado);
                }
                if ($valida_despacho) {
                    $sub->where("despacho_id", null);
                }
                $sub->where("status", 1);
            });
        })->distinct()
            ->orderBy("nombre", "asc")->get()
            ->map(function ($categoria) use ($segmentacion_zona_ids, $estado, $valida_despacho, $despacho_id) {
                $categoria->productos = Producto::whereHas("pedido_detalles", function ($q) use ($categoria, $segmentacion_zona_ids, $estado, $valida_despacho, $despacho_id) {
                    $q->where("categoria_producto_id", $categoria->id);
                    $q->whereHas("pedido", function ($sub) use ($segmentacion_zona_ids, $estado, $valida_despacho, $despacho_id) {
                        if ($despacho_id) {
                            $sub->where("despacho_id", $despacho_id);
                        }
                        if ($segmentacion_zona_ids !== null) {
                            $sub->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
                        }
                        if ($estado) {
                            $sub->where("estado", $estado);
                        }
                        if ($valida_despacho) {
                            $sub->where("despacho_id", null);
                        }
                        $sub->where("status", 1);
                    });
                })->orderBy("nombre", "asc")
                    ->get()
                    ->map(function ($producto) use ($segmentacion_zona_ids, $estado, $valida_despacho, $despacho_id) {
                        $producto->ver = false;

                        $producto->pedido_detalles = PedidoDetalle::with(["pedido.cliente", "presentacion_producto"])->whereHas("pedido", function ($q) use ($segmentacion_zona_ids, $estado, $valida_despacho, $despacho_id) {
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($segmentacion_zona_ids !== null) {
                                $q->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
                            }
                            if ($estado) {
                                $q->where("estado", $estado);
                            }
                            if ($valida_despacho) {
                                $q->where("despacho_id", null);
                            }
                            $q->where("status", 1);
                        })->where("producto_id", $producto->id)->get();

                        $producto->cantidad_total = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($segmentacion_zona_ids, $estado, $valida_despacho, $despacho_id) {
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($segmentacion_zona_ids !== null) {
                                $q->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
                            }
                            if ($estado) {
                                $q->where("estado", $estado);
                            }
                            if ($valida_despacho) {
                                $q->where("despacho_id", null);
                            }
                            $q->where("status", 1);
                        })->where("producto_id", $producto->id)->sum("cantidad_total");
                        $producto->cantidad_despacho = $producto->cantidad_total;
                        $producto->stock_previsto = $producto->stock_actual - $producto->cantidad_despacho;
                        return $producto;
                    });
                return $categoria;
            });
    }

    public function pedido_preparacion_general($fecha = null)
    {
        return CategoriaProducto::whereHas("pedido_detalles", function ($q) use ($fecha) {
            $q->whereHas("pedido", function ($sub) use ($fecha) {
                $sub->where("estado", "PENDIENTE");
                $sub->where("status", 1);
                if ($fecha) {
                    $sub->whereDate("created_at", $fecha);
                }
            });
        })->distinct()
            ->orderBy("nombre", "asc")->get()
            ->map(function ($categoria) use ($fecha) {
                $categoria->productos = Producto::whereHas("pedido_detalles", function ($q) use ($categoria, $fecha) {
                    $q->where("categoria_producto_id", $categoria->id);
                    $q->whereHas("pedido", function ($sub) use ($fecha) {
                        $sub->where("estado", "PENDIENTE");
                        $sub->where("status", 1);
                        if ($fecha) {
                            $sub->whereDate("created_at", $fecha);
                        }
                    });
                })->orderBy("nombre", "asc")
                    ->get()
                    ->map(function ($producto) use ($fecha) {
                        $producto->ver = false;

                        $producto->pedido_detalles = PedidoDetalle::with(["pedido.cliente", "presentacion_producto"])->whereHas("pedido", function ($q) use ($fecha) {
                            $q->where("estado", "PENDIENTE");
                            $q->where("status", 1);
                            if ($fecha) {
                                $q->whereDate("created_at", $fecha);
                            }
                        })->where("producto_id", $producto->id)->get();

                        $producto->cantidad_total = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($fecha) {
                            $q->where("estado", "PENDIENTE");
                            $q->where("status", 1);
                            if ($fecha) {
                                $q->whereDate("created_at", $fecha);
                            }
                        })->where("producto_id", $producto->id)->sum("cantidad_total");
                        $producto->cantidad_despacho = $producto->cantidad_total;
                        $producto->stock_previsto = $producto->stock_actual - $producto->cantidad_despacho;
                        return $producto;
                    });
                return $categoria;
            });
    }

    public function pedido_seleccionados($ids)
    {
        return CategoriaProducto::whereHas("pedido_detalles", function ($q) use ($ids) {
            $q->whereHas("pedido", function ($sub) use ($ids) {
                $sub->whereIn("id", $ids);
                $sub->where("status", 1);
            });
        })->distinct()
            ->orderBy("nombre", "asc")->get()
            ->map(function ($categoria) use ($ids) {
                $categoria->productos = Producto::whereHas("pedido_detalles", function ($q) use ($categoria, $ids) {
                    $q->where("categoria_producto_id", $categoria->id);
                    $q->whereHas("pedido", function ($sub) use ($ids) {
                        $sub->whereIn("id", $ids);
                        $sub->where("status", 1);
                    });
                })->orderBy("nombre", "asc")
                    ->get()
                    ->map(function ($producto) use ($ids) {
                        $producto->ver = false;

                        $producto->pedido_detalles = PedidoDetalle::with(["pedido.cliente", "presentacion_producto"])->whereHas("pedido", function ($q) use ($ids) {
                            $q->whereIn("id", $ids);
                            $q->where("status", 1);
                        })->where("producto_id", $producto->id)->get();

                        $producto->cantidad_total = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($ids) {
                            $q->whereIn("id", $ids);
                            $q->where("status", 1);
                        })->where("producto_id", $producto->id)->sum("cantidad_total");
                        $producto->cantidad_despacho = $producto->cantidad_total;
                        $producto->stock_previsto = $producto->stock_actual - $producto->cantidad_despacho;
                        return $producto;
                    });
                return $categoria;
            });
    }

    public function pedidos_despacho($consolidado_id = null, $despacho_id = null, $estado = "", $detalles = false)
    {
        return CategoriaProducto::whereHas("pedido_detalles", function ($q) use ($consolidado_id, $despacho_id, $estado, $detalles) {
            $q->whereHas("pedido", function ($sub) use ($consolidado_id, $despacho_id, $estado) {
                if ($consolidado_id) {
                    $sub->where("consolidado_id", $consolidado_id);
                }
                if ($despacho_id) {
                    $sub->where("despacho_id", $despacho_id);
                }
                $sub->whereHas("despacho", function ($sub2) use ($estado) {
                    if ($estado) {
                        $sub2->where("estado", $estado);
                    }
                });
                $sub->where("status", 1);
            });
        })->distinct()
            ->orderBy("nombre", "asc")->get()
            ->map(function ($categoria) use ($consolidado_id, $despacho_id, $estado, $detalles) {
                $categoria->productos = Producto::whereHas("pedido_detalles", function ($q) use ($categoria, $consolidado_id, $despacho_id, $estado, $detalles) {
                    $q->where("categoria_producto_id", $categoria->id);
                    $q->whereHas("pedido", function ($sub) use ($consolidado_id, $despacho_id, $estado) {
                        if ($consolidado_id) {
                            $sub->where("consolidado_id", $consolidado_id);
                        }
                        if ($despacho_id) {
                            $sub->where("despacho_id", $despacho_id);
                        }
                        if ($estado) {
                            $sub->whereHas("despacho", function ($sub2) use ($estado) {
                                $sub2->where("estado", $estado);
                            });
                        }
                        $sub->where("status", 1);
                    });
                })->orderBy("nombre", "asc")
                    ->get()
                    ->map(function ($producto) use ($consolidado_id, $despacho_id, $estado, $detalles) {
                        $producto->ver = $detalles;

                        $producto->pedido_detalles = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($consolidado_id, $despacho_id, $estado) {
                            if ($consolidado_id) {
                                $q->where("consolidado_id", $consolidado_id);
                            }
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($estado) {
                                $q->whereHas("despacho", function ($sub2) use ($estado) {
                                    $sub2->where("estado", $estado);
                                });
                            }
                            $q->where("status", 1);
                        })
                            ->where("producto_id", $producto->id)->get();

                        $producto->cantidad_despacho = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($consolidado_id, $despacho_id, $estado) {
                            if ($consolidado_id) {
                                $q->where("consolidado_id", $consolidado_id);
                            }
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($estado) {
                                $q->whereHas("despacho", function ($sub2) use ($estado) {
                                    $sub2->where("estado", $estado);
                                });
                            }
                            $q->where("status", 1);
                        })
                            ->where("producto_id", $producto->id)->sum("cantidad_despacho");

                        $producto->cantidad_entregado = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($consolidado_id, $despacho_id, $estado) {
                            if ($consolidado_id) {
                                $q->where("consolidado_id", $consolidado_id);
                            }
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($estado) {
                                $q->whereHas("despacho", function ($sub2) use ($estado) {
                                    $sub2->where("estado", $estado);
                                });
                            }
                            $q->where("status", 1);
                        })
                            ->where("producto_id", $producto->id)->sum("cantidad_entregado");

                        $producto->cantidad_devolucion = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($consolidado_id, $despacho_id, $estado) {
                            if ($consolidado_id) {
                                $q->where("consolidado_id", $consolidado_id);
                            }
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($estado) {
                                $q->whereHas("despacho", function ($sub2) use ($estado) {
                                    $sub2->where("estado", $estado);
                                });
                            }
                            $q->where("status", 1);
                        })
                            ->where("producto_id", $producto->id)->sum("cantidad_devolucion");

                        $producto->subtotal = PedidoDetalle::with("pedido.cliente")->whereHas("pedido", function ($q) use ($consolidado_id, $despacho_id, $estado) {
                            if ($consolidado_id) {
                                $q->where("consolidado_id", $consolidado_id);
                            }
                            if ($despacho_id) {
                                $q->where("despacho_id", $despacho_id);
                            }
                            if ($estado) {
                                $q->whereHas("despacho", function ($sub2) use ($estado) {
                                    $sub2->where("estado", $estado);
                                });
                            }
                            $q->where("status", 1);
                        })
                            ->where("producto_id", $producto->id)->sum("subtotal");

                        return $producto;
                    });
                return $categoria;
            });
    }

    public function asignar_pedidos($data)
    {
        $distribuidor_id = $data["distribuidor_id"];
        $ids = $data["ids"];

        // Log::debug($distribuidor_id);
        // Log::debug($ids);
        foreach ($ids as $id) {
            $pedido = Pedido::find($id);

            $pedido->user_distribucion_id = $distribuidor_id;
            $pedido->fecha_salida = date("Y-m-d");
            $pedido->hora_salida = date("H:i:s");

            $pedido->save();
        }
    }

    public function pedido_seleccionados_cliente($params)
    {
        $pedidosQuery = Pedido::with('cliente')->where("status", 1)->whereNotNull("despacho_id");

        if (!empty($params['ids'])) {
            $ids = explode(',', $params['ids']);
            $pedidosQuery->whereIn('id', $ids);
        } else {
            if (Auth::user()->tipo != 'ADMINISTRADOR') {
                $segmentacion_zona_ids = $this->user_service->getSegmentacionZona(Auth::user()->id);
                $pedidosQuery->whereIn("segmentacion_zona_id", $segmentacion_zona_ids);
            }
            if (!empty($params['producto_id'])) {
                $producto_id = $params['producto_id'];
                $pedidosQuery->whereHas("pedido_detalles", function ($query) use ($producto_id) {
                    $query->where("producto_id", $producto_id);
                });
            }
            if (!empty($params['cliente_id'])) {
                $pedidosQuery->where("cliente_id", $params['cliente_id']);
            }
            if (!empty($params['fecha_ini']) && !empty($params['fecha_fin'])) {
                $pedidosQuery->whereBetween("fecha", [$params['fecha_ini'], $params['fecha_fin']]);
            }
            if (!empty($params['search'])) {
                $search = $params['search'];
                $pedidosQuery->where(function ($query) use ($search) {
                    $query->orWhere("id", "LIKE", "%$search%");
                    $query->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%$search%");
                    });
                });
            }
        }

        $pedidos = $pedidosQuery->orderBy('cliente_id')->get();
        
        $clientes = $pedidos->groupBy('cliente_id')->map(function ($clientPedidos) {
            $cliente = $clientPedidos->first()->cliente;
            $cliente->pedidos_lista = $clientPedidos;
            $cliente->subtotal_pedidos = $clientPedidos->sum('total');
            return $cliente;
        })->values();

        return $clientes;

    }

    /**
     * Recalcular totales en cascada para pedidos activos si cambia el precio de la presentación
     */
    public function recalcularTotalesPorPresentacion($presentacion_producto_id, $nuevo_precio)
    {
        // Encontrar los PedidoDetalle afectados en pedidos PENDIENTES
        $detalles = PedidoDetalle::where('presentacion_producto_id', $presentacion_producto_id)
            ->whereHas('pedido', function($query) {
                $query->where('estado', 'PENDIENTE');
            })
            ->get();

        $pedidos_afectados = [];

        foreach ($detalles as $detalle) {
            $detalle->precio = $nuevo_precio;
            $detalle->subtotal = (float)$detalle->cantidad * (float)$nuevo_precio;
            $detalle->save();

            if (!in_array($detalle->pedido_id, $pedidos_afectados)) {
                $pedidos_afectados[] = $detalle->pedido_id;
            }
        }

        // Recalcular los pedidos afectados
        foreach ($pedidos_afectados as $pedido_id) {
            $pedido = Pedido::find($pedido_id);
            if ($pedido) {
                $nuevo_subtotal_pedido = PedidoDetalle::where('pedido_id', $pedido->id)->sum('subtotal');
                $pedido->subtotal = $nuevo_subtotal_pedido;
                $pedido->total = (float)$nuevo_subtotal_pedido - (float)$pedido->descuento;
                $pedido->save();
            }
        }
    }
}
