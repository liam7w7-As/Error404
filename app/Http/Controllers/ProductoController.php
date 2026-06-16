<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoStoreRequest;
use App\Http\Requests\ProductoUpdateRequest;
use App\Models\Producto;
use App\Models\User;
use App\Services\ProductoService;
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

class ProductoController extends Controller
{
    public function __construct(private ProductoService $productoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Productos/Index");
    }

    /**
     * Listado de productos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "productos" => $this->productoService->listado($request->input("stock_pendientes", false))
        ]);
    }

    public function listadoStockBajo(Request $request)
    {
        $productos  = Producto::select("productos.*")
            ->with(["categoria_producto:id,nombre"])
            ->where("estado", 1);

        if (isset($request->categoria_producto_id) || isset($request->producto_id)) {
            $productos->when($request->categoria_producto_id, function ($q) use ($request) {
                $q->where("categoria_producto_id", $request->categoria_producto_id);
            });
            $productos->when($request->producto_id, function ($q) use ($request) {
                $q->where("id", $request->producto_id);
            });
        } else {
            $productos->where("stock_actual", "<=", "stock_min");
        }
        $productos = $productos->orderBy("stock_actual", "asc")->get()
            ->map(function ($producto) {
                $stock_previsto = $producto->stock_actual;
                $producto->stock_previsto_aux = $stock_previsto;
                $producto->stock_previsto = $stock_previsto;
                $producto->cantidad = 0;
                $producto->precio_compra = $producto->precio_compra ? (float)$producto->precio_compra : 0;
                $producto->total = 0;
                return $producto;
            });
        return response()->JSON([
            "productos" => $productos
        ]);
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
            "descripcion",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $productos = $this->productoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $productos->items(),
            "total" => $productos->total(),
            "lastPage" => $productos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Producto
            $this->productoService->crear($request->validated());
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un producto
     *
     * @param Producto $producto
     * @return JsonResponse
     */
    public function show(Producto $producto): JsonResponse
    {
        return response()->JSON($producto);
    }

    public function update(Producto $producto, ProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar producto
            $this->productoService->actualizar($request->validated(), $producto);
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return JsonResponse|Response
     */
    public function destroy(Producto $producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->productoService->eliminar($producto);
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
