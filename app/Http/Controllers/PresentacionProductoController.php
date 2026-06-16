<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresentacionProductoStoreRequest;
use App\Http\Requests\PresentacionProductoUpdateRequest;
use App\Models\PresentacionProducto;
use App\Models\User;
use App\Services\PresentacionProductoService;
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

class PresentacionProductoController extends Controller
{
    public function __construct(private PresentacionProductoService $presentacion_productoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/PresentacionProductos/Index");
    }

    /**
     * Listado de presentacion_productos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {

        return response()->JSON([
            "presentacion_productos" => $this->presentacion_productoService->listado(isset($request->producto_id) ? $request->producto_id : 0)
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

        $presentacion_productos = $this->presentacion_productoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $presentacion_productos->items(),
            "total" => $presentacion_productos->total(),
            "lastPage" => $presentacion_productos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo presentacion_producto
     *
     * @param PresentacionProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(PresentacionProductoStoreRequest $request): RedirectResponse|Response|JsonResponse
    {
        DB::beginTransaction();
        try {
            // crear el PresentacionProducto
            $this->presentacion_productoService->crear($request->validated());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro realizado',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un presentacion_producto
     *
     * @param PresentacionProducto $presentacion_producto
     * @return JsonResponse
     */
    public function show(PresentacionProducto $presentacion_producto): JsonResponse
    {
        return response()->JSON($presentacion_producto);
    }

    public function update(PresentacionProducto $presentacion_producto, PresentacionProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar presentacion_producto
            $this->presentacion_productoService->actualizar($request->validated(), $presentacion_producto);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro realizado',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar presentacion_producto
     *
     * @param PresentacionProducto $presentacion_producto
     * @return JsonResponse|Response
     */
    public function destroy(PresentacionProducto $presentacion_producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->presentacion_productoService->eliminar($presentacion_producto);
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
