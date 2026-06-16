<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Despacho</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 1cm;
            margin-bottom: 0.3cm;
            margin-left: 1.3cm;
            margin-right: 0.3cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-before: avoid;
        }

        table thead tr th,
        tbody tr td {
            padding: 3px;
            word-wrap: break-word;
        }

        table thead tr th {
            font-size: 9pt;
        }

        table tbody tr td {
            font-size: 8pt;
        }


        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            height: 90px;
            top: -25px;
            left: 0px;
        }

        h2.titulo {
            width: 700px;
            margin: auto;
            margin-top: 0PX;
            text-align: center;
            font-size: 14pt;
        }

        .texto {
            width: 700px;
            text-align: center;
            margin: auto;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 250px;
            text-align: center;
            margin: auto;
            font-weight: normal;
            font-size: 0.85em;
        }

        .total {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
        }

        table thead {
            background: rgb(236, 236, 236)
        }

        tr {
            page-break-inside: avoid !important;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .txt_center {
            font-weight: bold;
            text-align: center;
        }


        .gray {
            background: rgb(240, 240, 240);
        }

        .bg-principal {
            background: #153f59;
            color: white;
        }

        .img_celda img {
            width: 45px;
        }

        .derecha {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .normal {
            font-weight: normal;
        }

        .lista {
            padding-left: 4px;
            margin-left: 8px;
        }

        .text-md {
            font-size: 10pt;
        }

        .text-lg {
            font-size: 13pt;
        }

        .border-bottom {
            border-bottom: solid 1px black;
        }

        .border-top {
            border-top: solid 1px black;
        }
    </style>
</head>

<body>
    @inject('configuracion', 'App\Models\Configuracion')
    <div class="encabezado">
        <div class="logo">
            <img src="{{ $configuracion->first()->logo_b64 }}">
        </div>
        <h2 class="titulo">
            {{ $configuracion->first()->razon_social }}
        </h2>
        <h4 class="texto">Reporte de Despachos por Producto</h4>
        {{-- <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4> --}}
    </div>
    <table border="0" style="margin-top: 60px;width:100%;">
        <tr>
            <td width="75%">
                <strong>Distribuidor:</strong> {{ isset($despacho) ? $despacho->distribuidor->nombre : 'GENERAL (TODOS LOS PENDIENTES)' }}
            </td>
            <td width="">
                <strong>Fecha Imp.:</strong> {{ date('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Fecha Pedido:</strong> {{ isset($despacho) ? $despacho->fecha_t : date('d/m/Y') }}
            </td>
            <td>
                <strong>Hora Imp.:</strong> {{ date('H:i') }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td><strong>Usuario: </strong>{{ Auth::user()->usuario }}</td>
        </tr>
    </table>

    @foreach ($categoria_productos as $item)
        <table style="margin-bottom:20px;">
            <tbody>
                <tr>
                    <td width="15%"></td>
                    <td></td>
                    <td width="20%"></td>
                </tr>
                <tr>
                    <td colspan="3" class="bold border-bottom text-md border-top">CATEGORÍA: <span
                            class="normal">{{ $item->nombre }}</span></td>
                </tr>
                <tr class="border-bottom">
                    <td class="bold">Código</td>
                    <td class="bold">Nombre</td>
                    <td class="bold derecha">Total</td>
                </tr>
                @php
                    $total = 0;
                @endphp
                @foreach ($item['productos'] as $producto_categoria)
                    <tr>
                        <td>
                            <span class="">
                                {{ $producto_categoria->id }}</span>
                        </td>
                        <td>
                            <span class="">
                                {{ $producto_categoria->nombre }}</span>
                        </td>
                        {{-- <td class="centreado">
                            {{ $producto_categoria->cantidad_total }}
                        </td> --}}
                        <td class="derecha">
                            {{ $producto_categoria->cantidad_despacho }}
                        </td>
                    </tr>
                    @php
                        $total += (float) $producto_categoria->cantidad_despacho;
                    @endphp
                @endforeach
                <tr class="border-top">
                    <td colspan="2" class="derecha bold">TOTAL</td>
                    <td class="bold derecha">{{ $total }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>

</html>
