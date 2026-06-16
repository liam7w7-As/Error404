<?php

namespace App\Http\Controllers;

use App\Http\Requests\SegmentacionZonaStoreRequest;
use App\Http\Requests\SegmentacionZonaUpdateRequest;
use App\Models\SegmentacionZona;
use App\Models\User;
use App\Services\SegmentacionZonaService;
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

class SegmentacionZonaController extends Controller
{
    public function __construct(private SegmentacionZonaService $segmentacion_zonaService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/SegmentacionZonas/Index");
    }

    /**
     * Listado de segmentacion_zonas sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "segmentacion_zonas" => $this->segmentacion_zonaService->listado()
        ]);
    }
    public function listadoSegmentacion(Request $request): JsonResponse
    {
        $segmentacion_zonas = SegmentacionZona::select("segmentacion_zonas.*");
        if (isset($request->id)) {
            $segmentacion_zonas->where("id", "!=", $request->id);
        }
        $segmentacion_zonas = $segmentacion_zonas->get();
        return response()->JSON([
            "segmentacion_zonas" => $segmentacion_zonas
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

        $segmentacion_zonas = $this->segmentacion_zonaService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $segmentacion_zonas->items(),
            "total" => $segmentacion_zonas->total(),
            "lastPage" => $segmentacion_zonas->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo segmentacion_zona
     *
     * @param SegmentacionZonaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(SegmentacionZonaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el SegmentacionZona
            $this->segmentacion_zonaService->crear($request->validated());
            DB::commit();
            return redirect()->route("segmentacion_zonas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un segmentacion_zona
     *
     * @param SegmentacionZona $segmentacion_zona
     * @return JsonResponse
     */
    public function show(SegmentacionZona $segmentacion_zona): JsonResponse
    {
        return response()->JSON($segmentacion_zona);
    }

    public function update(SegmentacionZona $segmentacion_zona, SegmentacionZonaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar segmentacion_zona
            $this->segmentacion_zonaService->actualizar($request->validated(), $segmentacion_zona);
            DB::commit();
            return redirect()->route("segmentacion_zonas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar segmentacion_zona
     *
     * @param SegmentacionZona $segmentacion_zona
     * @return JsonResponse|Response
     */
    public function destroy(SegmentacionZona $segmentacion_zona): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->segmentacion_zonaService->eliminar($segmentacion_zona);
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
