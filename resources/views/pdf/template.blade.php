<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Documento</title>
    <style>
        body {
            margin-top: 100px;
            font-family: 'Arial', sans-serif;
        }

        #header {
            width: 100%;
            text-align: center;
            position: fixed;
            top: 0;
            padding: 10px;
            height: 60px;
        }

        #footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;

        }

        #content {
            width: 100%;
            margin-top: 10px;
            margin-left: 20px;
        }

        b {
            font-size: 14px;
            /* Puedes ajustar el tamaño de la fuente según tus necesidades */
        }

        i {
            font-size: 11px;
        }

        p,
        li {
            font-size: 12px;
        }


        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
        }

        table,
        th,
        td {
            border: 1px solid black;
            font-size: 12px;
        }
    </style>

</head>

<body>

    <div id="header">
        <!-- Contenido del encabezado -->
        @include('pdf.header')
    </div>

    <div id="footer">
        <!-- Contenido del pie de página -->
        @include('pdf.footer2')
    </div>

    <div class="content">
        @include('pdf.content')

    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("sans-serif", "normal");
            $pdf->text(471, 87, "", $font, 9);
            $pdf->text(471, 87, "Página $PAGE_NUM de $PAGE_COUNT", $font, 9);
        ');
    }
</script>

</body>

</html>