<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidadoStoreRequest;
use App\Models\Consolidado;
use App\Services\ConsolidadoService;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use PDF;

class ConsolidadoController extends Controller
{
    public function __construct(private ConsolidadoService $consolidadoService, private PedidoService $pedidoService) {}
    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Consolidados/Index");
    }


    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

        $columnsSerachLike = [
            "id",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $clientes = $this->consolidadoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $clientes->items(),
            "total" => $clientes->total(),
            "lastPage" => $clientes->lastPage()
        ]);
    }

    public function ver(Consolidado $consolidado): ResponseInertia
    {
        $consolidado = $consolidado->load(["distribuidor"]);
        $categoria_productos = $this->pedidoService->pedidos_despacho($consolidado->id, $consolidado->despacho_id);

        return Inertia::render("Admin/Consolidados/Ver", compact("consolidado", "categoria_productos"));
    }

    public function pdf(Consolidado $consolidado)
    {
        $consolidado = $consolidado->load(["distribuidor"]);
        $categoria_productos = $this->pedidoService->pedidos_despacho($consolidado->id, $consolidado->despacho_id);

        $pdf = PDF::loadView('reportes.consolidado', compact('consolidado', 'categoria_productos'))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('consolidado.pdf');
    }

    /**
     * Página create
     *
     * @return Response
     */
    public function create(): ResponseInertia
    {
        return Inertia::render("Admin/Consolidados/Create");
    }

    /**
     * Registrar un nuevo consolidado
     *
     * @param ConsolidadoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ConsolidadoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Consolidado
            $this->consolidadoService->crear($request->validated());
            DB::commit();
            return redirect()->route("consolidados.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
