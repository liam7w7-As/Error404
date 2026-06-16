<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompraStoreRequest;
use App\Http\Requests\CompraUpdateRequest;
use App\Models\Compra;
use App\Models\User;
use App\Services\CompraService;
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

class CompraController extends Controller
{
    public function __construct(private CompraService $compraService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Compras/Index");
    }

    /**
     * Listado de compras sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "compras" => $this->compraService->listado()
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

        $compras = $this->compraService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $compras->items(),
            "total" => $compras->total(),
            "lastPage" => $compras->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo compra
     *
     * @param CompraStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(CompraStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Compra
            $this->compraService->crear($request->validated());
            DB::commit();
            return redirect()->route("compras.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un compra
     *
     * @param Compra $compra
     * @return JsonResponse
     */
    public function show(Compra $compra): JsonResponse
    {
        return response()->JSON($compra);
    }

    public function update(Compra $compra, CompraUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar compra
            $this->compraService->actualizar($request->validated(), $compra);
            DB::commit();
            return redirect()->route("compras.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar compra
     *
     * @param Compra $compra
     * @return JsonResponse|Response
     */
    public function destroy(Compra $compra): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->compraService->eliminar($compra);
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
