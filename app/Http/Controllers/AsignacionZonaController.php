<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignacionZonaStoreRequest;
use App\Http\Requests\AsignacionZonaUpdateRequest;
use App\Models\AsignacionZona;
use App\Models\User;
use App\Services\AsignacionZonaService;
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

class AsignacionZonaController extends Controller
{
    public function __construct(private AsignacionZonaService $asignacion_zonaService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/AsignacionZonas/Index");
    }

    /**
     * Listado de asignacion_zonas sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "asignacion_zonas" => $this->asignacion_zonaService->listado()
        ]);
    }
    public function listadoSegmentacion(Request $request): JsonResponse
    {
        $asignacion_zonas = AsignacionZona::select("asignacion_zonas.*");
        if (isset($request->id)) {
            $asignacion_zonas->where("id", "!=", $request->id);
        }
        $asignacion_zonas = $asignacion_zonas->get();
        return response()->JSON([
            "asignacion_zonas" => $asignacion_zonas
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

        $asignacion_zonas = $this->asignacion_zonaService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $asignacion_zonas->items(),
            "total" => $asignacion_zonas->total(),
            "lastPage" => $asignacion_zonas->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo asignacion_zona
     *
     * @param AsignacionZonaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(AsignacionZonaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el AsignacionZona
            $this->asignacion_zonaService->crear($request->validated());
            DB::commit();
            return redirect()->route("asignacion_zonas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un asignacion_zona
     *
     * @param AsignacionZona $asignacion_zona
     * @return JsonResponse
     */
    public function show(AsignacionZona $asignacion_zona): JsonResponse
    {
        return response()->JSON($asignacion_zona);
    }

    public function update(AsignacionZona $asignacion_zona, AsignacionZonaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar asignacion_zona
            $this->asignacion_zonaService->actualizar($request->validated(), $asignacion_zona);
            DB::commit();
            return redirect()->route("asignacion_zonas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar asignacion_zona
     *
     * @param AsignacionZona $asignacion_zona
     * @return JsonResponse|Response
     */
    public function destroy(AsignacionZona $asignacion_zona): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->asignacion_zonaService->eliminar($asignacion_zona);
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
