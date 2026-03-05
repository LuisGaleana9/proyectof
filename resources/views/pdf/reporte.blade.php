<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividades</title>
    <style>
        @page {
            /* Márgenes estándar, pero puedes reducirlos si necesitas más espacio */
            margin: 1.5cm 2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px; /* Tamaño de letra compacto */
            line-height: 1.2; /* Interlineado estrecho */
            color: #000;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 8px; /* Espacio mínimo entre tablas */
        }
        td {
            padding: 1px 2px; /* Relleno mínimo en las celdas */
            vertical-align: top;
            border: none;
        }
        
        /* Utilidades */
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Estilos del Encabezado */
        .titulo-uni {
            font-size: 14px;
            font-weight: bold;
        }
        .subtitulo-dep {
            font-size: 12px;
            font-weight: bold;
        }
        .datos-contacto {
            font-size: 9px;
            font-style: italic;
        }
        .titulo-reporte {
            font-size: 13px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        /* Bloque de contenido principal */
        .texto-actividades {
            margin-top: 15px;
            margin-bottom: 35px;
            line-height: 1.3;
        }

        /* Sección de Firmas */
        .tabla-firmas {
            font-size: 10px;
            margin-bottom: 35px; /* Espacio entre la primera y segunda fila de firmas */
        }
    </style>
</head>
<body>

    <table>
        <tr>
            <td width="20%" class="text-center" style="vertical-align: middle;">
                <img src="{{ public_path('images/escudo.png') }}" width="115" alt="UMSNH">
            </td>
            <td width="80%" class="text-center">
                <div class="titulo-uni">UNIVERSIDAD MICHOACANA DE SAN NICOLÁS DE HIDALGO</div>
                <div class="subtitulo-dep">DIRECCIÓN DE SERVICIO SOCIAL</div>
                <div class="datos-contacto">Edificio "C7"<br>Tel: 3223500 ext.3066</div>
            </td>
        </tr>
    </table>

    <div class="text-center titulo-reporte uppercase">REPORTE DE ACTIVIDADES</div>

    <table>
        <tr>
            <td width="15%" class="bold">Reporte No.:</td>
            <td width="35%">{{ $reporte->numero ?? '1' }}</td>
            <td width="15%" class="bold">Fecha de entrega:</td>
            <td width="35%">{{ $reporte->fecha_entrega->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="bold">Nombre del<br>pasante:</td>
            <td class="uppercase">{{ $alumno->nombre }} {{ $alumno->apellidos_p }} {{ $alumno->apellidos_m }}</td>
            <td class="bold">Matrícula:</td>
            <td>{{ $alumno->matricula }}</td>
        </tr>
        <tr>
            <td class="bold">Carrera:</td>
            <td>{{ $alumno->carrera ?? 'Licenciatura en Ingeniería en Computación' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="15%" class="bold">Unidad de<br>adscripción:</td>
            <td width="35%">{{ $servicio->unidad ?? 'Facultad de Ingeniería Eléctrica' }}</td>
            <td width="15%" class="bold">Programa:</td>
            <td width="35%" class="uppercase text-justify">{{ $servicio->nombre }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="20%" class="bold">Período que reporta:</td>
            <td width="80%">del {{ $inscripcion->fecha_inicio->format('Y-m-d') }} al {{ $inscripcion->fecha_inicio->copy()->addMonths(6)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="bold">Horas acumuladas a<br>la fecha:</td>
            <td style="vertical-align: middle;">{{ $reporte->horas_acumuladas ?? '160' }}</td>
        </tr>
        <tr>
            <td class="bold">Avance del<br>programa:</td>
            <td style="vertical-align: middle;">{{ $reporte->avance_porcentaje ?? '33' }} %</td>
        </tr>
    </table>

    <table style="margin-top: 15px;">
        <tr>
            <td class="bold text-center" width="33%">N. Mujeres Beneficiadas por<br>primera vez</td>
            <td class="bold text-center" width="33%">N. Hombres Beneficiados por<br>primera vez</td>
            <td class="bold text-center" width="34%"><br>Total Beneficiados</td>
        </tr>
        <tr>
            <td class="text-center">{{ $reporte->mujeres_beneficiadas ?? '0' }}</td>
            <td class="text-center">{{ $reporte->hombres_beneficiados ?? '0' }}</td>
            <td class="text-center">{{ $reporte->total_beneficiados ?? '0' }}</td>
        </tr>
    </table>

    <div class="texto-actividades text-justify">
        {{ $reporte->contenido }}
    </div>

    <table class="tabla-firmas text-center">
        <tr>
            <td width="35%">
                <div class="uppercase">{{ $alumno->nombre }} {{ $alumno->apellidos_p }} {{ $alumno->apellidos_m }}</div>
                <div>Nombre del pasante</div>
            </td>
            <td width="35%">
                <div class="uppercase">{{ $servicio->asesor ?? 'RODRIGO GUZMAN MALDONADO' }}</div>
                <div>Asesor de Servicio Social</div>
            </td>
            <td width="30%">
                <div class="uppercase">SELLO DE LA DEPENDENCIA</div>
            </td>
        </tr>
    </table>

    <table class="text-center" style="font-size: 10px;">
        <tr>
            <td width="50%">
                <div>Vo. Bo. Coordinador de la Dirección de Servicio Social</div>
            </td>
            <td width="50%">
                <div>Vo. Bo. Coordinador de la Unidad Académica</div>
            </td>
        </tr>
    </table>

</body>
</html>