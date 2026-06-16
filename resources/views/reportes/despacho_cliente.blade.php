<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Despachos por Cliente</title>
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
        <h4 class="texto">REPORTE DE DESPACHO POR CLIENTE</h4>
        <h4 class="fecha">Fechas: {{ date('d/m/Y', strtotime($fecha_ini)) }} - {{ date('d/m/Y', strtotime($fecha_fin)) }}</h4>
    </div>
    <table border="0" style="margin-top: 20px;width:100%;">
        <tr>
            <td width="75%">
                <strong>Fecha Gen.:</strong> {{ date('d/m/Y') }}
            </td>
            <td width="">
                <strong>Hora Gen.:</strong> {{ date('H:i') }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>Usuario: </strong>{{ Auth::user()->usuario }}
            </td>
            <td></td>
        </tr>
    </table>

    @php
        $total_general = 0;
    @endphp

    @foreach ($clientes as $cliente)
        <table style="margin-bottom:20px; margin-top:20px;">
            <tbody>
                <tr>
                    <td width="15%"></td>
                    <td width="35%"></td>
                    <td width="20%"></td>
                    <td width="30%"></td>
                </tr>
                <tr>
                    <td colspan="4" class="bold border-bottom text-md border-top">CLIENTE: <span class="normal">{{ $cliente->nombre }}</span></td>
                </tr>
                <tr class="border-bottom">
                    <td class="bold">Nro de Pedido</td>
                    <td class="bold">Fecha</td>
                    <td class="bold">Observación</td>
                    <td class="bold derecha">Total Bs</td>
                </tr>
                @foreach ($cliente->pedidos_lista as $pedido)
                    <tr>
                        <td>{{ $pedido->id }}</td>
                        <td>{{ date('d/m/Y', strtotime($pedido->fecha)) }}</td>
                        <td>{{ $pedido->observacion ?? '-' }}</td>
                        <td class="derecha">{{ number_format($pedido->total, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="bold derecha text-md">SUBTOTAL CLIENTE:</td>
                    <td class="bold derecha text-md">{{ number_format($cliente->subtotal_pedidos, 2, '.', ',') }}</td>
                </tr>
            </tbody>
        </table>
        @php
            $total_general += $cliente->subtotal_pedidos;
        @endphp
    @endforeach

    <table style="margin-top:20px;">
        <tr>
            <td class="bold derecha text-lg" width="70%">TOTAL GENERAL Bs:</td>
            <td class="bold derecha text-lg" width="30%">{{ number_format($total_general, 2, '.', ',') }}</td>
        </tr>
    </table>
</body>

</html>
