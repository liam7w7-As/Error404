<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComisionStoreRequest;
use App\Models\CategoriaProducto;
use App\Models\Comision;
use App\Models\ComisionDetalle;
use App\Models\PedidoDetalle;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Services\ComisionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use PDF;

class ComisionController extends Controller
{
    public function __construct(private ComisionService $comisionService) {}
    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Comisions/Index");
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

        $clientes = $this->comisionService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
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
        return Inertia::render("Admin/Comisions/Create");
    }

    /**
     * Registrar un nuevo comision
     *
     * @param ComisionStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ComisionStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Comision
            $this->comisionService->crear($request->validated());
            DB::commit();
            return redirect()->route("comisions.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function ver(Comision $comision): ResponseInertia
    {
        $comision = $comision->load(["distribuidor"]);

        $comision_id = $comision->id;
        $distribuidor_id = $comision->distribuidor_id;
        $comision_detalles = $this->comisionService->consultaComision($comision_id, $distribuidor_id);

        return Inertia::render("Admin/Comisions/Ver", compact("comision", "comision_detalles"));
    }

    public function pdf(Comision $comision)
    {
        $comision = $comision->load(["distribuidor"]);

        $comision_id = $comision->id;
        $distribuidor_id = $comision->distribuidor_id;
        $comision_detalles = $this->comisionService->consultaComision($comision_id, $distribuidor_id);

        $pdf = PDF::loadView('reportes.comision', compact('comision', 'comision_detalles'))->setPaper('letter', 'portrait');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('comision.pdf');
    }
}
