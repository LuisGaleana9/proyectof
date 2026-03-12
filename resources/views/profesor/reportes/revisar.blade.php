@extends('profesor.layout')

@section('content')
<div class="actions" style="justify-content: space-between; margin-bottom: 1rem;">
    <div>
        <h2 style="margin: 0;">Revisar {{ $reporte->nombreReporte() }}</h2>
        <p class="muted" style="margin: 0.25rem 0 0;">
            {{ $alumno->nombre ?? '' }}
            {{ $alumno->apellidos_p ?? '' }}
            · {{ $servicio->nombre ?? '' }}
        </p>
    </div>
    <a href="{{ route('profesor.reportes.index') }}" class="btn btn-secondary">Volver a reportes</a>
</div>

{{-- Información rápida del reporte --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="grid-2" style="margin-bottom: 1rem;">
        <div>
            <p class="muted" style="margin: 0;">Reporte</p>
            <p style="margin: 0.25rem 0;">
                <strong>#{{ $reporte->numero_reporte }}</strong> ·
                <span class="badge {{ $reporte->tipo === 'General' ? 'badge-warning' : 'badge-info' }}">{{ $reporte->tipo }}</span>
            </p>
        </div>
        <div>
            <p class="muted" style="margin: 0;">Fecha límite SIIA</p>
            <p style="margin: 0.25rem 0;"><strong>{{ $reporte->fecha_entrega->locale('es')->translatedFormat('d \\d\\e F \\d\\e Y') }}</strong></p>
            @php
                $diasRestantes = now()->startOfDay()->diffInDays($reporte->fecha_entrega, false);
            @endphp
            @if($diasRestantes > 0)
                <p class="muted" style="margin: 0;">{{ $diasRestantes }} días restantes</p>
            @elseif($diasRestantes == 0)
                <p style="margin: 0; color: #92400e;"><strong>¡Hoy vence!</strong></p>
            @else
                <p style="margin: 0; color: #991b1b;"><strong>Vencido hace {{ abs($diasRestantes) }} días</strong></p>
            @endif
        </div>
    </div>
    <div>
        <p class="muted" style="margin: 0;">Enviado el</p>
        <p style="margin: 0.25rem 0;">{{ $reporte->fecha_envio ? $reporte->fecha_envio->format('d/m/Y \\a \\l\\a\\s H:i') : '—' }}</p>
    </div>
</div>

{{-- Vista previa del reporte estilo PDF --}}
<div class="card" style="margin-bottom: 1.5rem; padding: 0; overflow: hidden;">
    <h3 style="margin: 0; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); background-color: #f9fafb;">
        Vista previa del reporte oficial
    </h3>
    <div style="padding: 1.5rem; display: flex; justify-content: center; background-color: #e5e7eb;">
        {{-- Contenedor estilo hoja de papel --}}
        <div style="background: #fff; width: 100%; max-width: 750px; padding: 40px 50px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.2; color: #000;">

            {{-- Encabezado institucional --}}
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                <tr>
                    <td style="width: 20%; text-align: center; vertical-align: middle; padding: 1px 2px; border: none;">
                        <img src="{{ asset('images/escudo.png') }}" style="width: 115px;" alt="UMSNH">
                    </td>
                    <td style="width: 80%; text-align: center; padding: 1px 2px; border: none;">
                        <div style="font-size: 14px; font-weight: bold;">UNIVERSIDAD MICHOACANA DE SAN NICOLÁS DE HIDALGO</div>
                        <div style="font-size: 12px; font-weight: bold;">DIRECCIÓN DE SERVICIO SOCIAL</div>
                        <div style="font-size: 9px; font-style: italic;">Edificio "C7"<br>Tel: 3223500 ext.3066</div>
                    </td>
                </tr>
            </table>

            <div style="text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 15px 0;">REPORTE DE ACTIVIDADES</div>

            {{-- Datos del reporte y alumno --}}
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                <tr>
                    <td style="width: 15%; font-weight: bold; padding: 1px 2px; border: none;">Reporte No.:</td>
                    <td style="width: 35%; padding: 1px 2px; border: none;">{{ $reporte->numero_reporte ?? '1' }}</td>
                    <td style="width: 15%; font-weight: bold; padding: 1px 2px; border: none;">Fecha de entrega:</td>
                    <td style="width: 35%; padding: 1px 2px; border: none;">{{ $reporte->fecha_entrega->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 1px 2px; border: none;">Nombre del<br>pasante:</td>
                    <td style="text-transform: uppercase; padding: 1px 2px; border: none;">{{ $alumno->nombre }} {{ $alumno->apellidos_p }} {{ $alumno->apellidos_m }}</td>
                    <td style="font-weight: bold; padding: 1px 2px; border: none;">Matrícula:</td>
                    <td style="padding: 1px 2px; border: none;">{{ $alumno->matricula }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 1px 2px; border: none;">Carrera:</td>
                    <td style="padding: 1px 2px; border: none;">{{ $alumno->carrera ?? 'Licenciatura en Ingeniería en Computación' }}</td>
                    <td style="padding: 1px 2px; border: none;"></td>
                    <td style="padding: 1px 2px; border: none;"></td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                <tr>
                    <td style="width: 15%; font-weight: bold; padding: 1px 2px; border: none;">Unidad de<br>adscripción:</td>
                    <td style="width: 35%; padding: 1px 2px; border: none;">{{ $servicio->unidad ?? 'Facultad de Ingeniería Eléctrica' }}</td>
                    <td style="width: 15%; font-weight: bold; padding: 1px 2px; border: none;">Programa:</td>
                    <td style="width: 35%; text-transform: uppercase; text-align: justify; padding: 1px 2px; border: none;">{{ $servicio->nombre }}</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                <tr>
                    <td style="width: 20%; font-weight: bold; padding: 1px 2px; border: none;">Período que reporta:</td>
                    <td style="width: 80%; padding: 1px 2px; border: none;">del {{ $inscripcion->fecha_inicio->format('Y-m-d') }} al {{ $inscripcion->fecha_inicio->copy()->addMonths(6)->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 1px 2px; border: none;">Horas acumuladas a<br>la fecha:</td>
                    <td style="vertical-align: middle; padding: 1px 2px; border: none;">{{ $reporte->horas_acumuladas ?? '160' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 1px 2px; border: none;">Avance del<br>programa:</td>
                    <td style="vertical-align: middle; padding: 1px 2px; border: none;">{{ $reporte->avance_porcentaje ?? '33' }} %</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 8px;">
                <tr>
                    <td style="font-weight: bold; text-align: center; width: 33%; padding: 1px 2px; border: none;">N. Mujeres Beneficiadas por<br>primera vez</td>
                    <td style="font-weight: bold; text-align: center; width: 33%; padding: 1px 2px; border: none;">N. Hombres Beneficiados por<br>primera vez</td>
                    <td style="font-weight: bold; text-align: center; width: 34%; padding: 1px 2px; border: none;"><br>Total Beneficiados</td>
                </tr>
                <tr>
                    <td style="text-align: center; padding: 1px 2px; border: none;">{{ $reporte->mujeres_beneficiadas ?? '0' }}</td>
                    <td style="text-align: center; padding: 1px 2px; border: none;">{{ $reporte->hombres_beneficiados ?? '0' }}</td>
                    <td style="text-align: center; padding: 1px 2px; border: none;">{{ $reporte->total_beneficiados ?? '0' }}</td>
                </tr>
            </table>

            {{-- Contenido del reporte --}}
            <div style="margin-top: 15px; margin-bottom: 35px; line-height: 1.3; text-align: justify; white-space: pre-wrap;">{{ $reporte->contenido }}</div>

            {{-- Firmas --}}
            <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 10px; margin-bottom: 35px;">
                <tr>
                    <td style="width: 35%; padding: 1px 2px; border: none;">
                        <div style="text-transform: uppercase;">{{ $alumno->nombre }} {{ $alumno->apellidos_p }} {{ $alumno->apellidos_m }}</div>
                        <div>Nombre del pasante</div>
                    </td>
                    <td style="width: 35%; padding: 1px 2px; border: none;">
                        <div style="text-transform: uppercase;">{{ $servicio->asesor ?? 'RODRIGO GUZMAN MALDONADO' }}</div>
                        <div>Asesor de Servicio Social</div>
                    </td>
                    <td style="width: 30%; padding: 1px 2px; border: none;">
                        <div style="text-transform: uppercase;">SELLO DE LA DEPENDENCIA</div>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 10px;">
                <tr>
                    <td style="width: 50%; padding: 1px 2px; border: none;">
                        <div>Vo. Bo. Coordinador de la Dirección de Servicio Social</div>
                    </td>
                    <td style="width: 50%; padding: 1px 2px; border: none;">
                        <div>Vo. Bo. Coordinador de la Unidad Académica</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>

@if($reporte->estado === 'Enviado')
    <div class="card">
        <h3 style="margin-top: 0;">Acciones de revisión</h3>
        <p class="muted" style="margin-bottom: 1rem;">
            Selecciona una acción para este reporte. Recuerda que debe ser enviado al SIIA antes del {{ $reporte->fecha_entrega->format('d/m/Y') }}.
        </p>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-start;">
            {{-- Aprobar --}}
            <form method="POST" action="{{ route('profesor.reportes.aprobar', $reporte->id) }}"
                  onsubmit="return confirm('¿Aprobar este reporte? El alumno podrá descargarlo como PDF.')">
                @csrf
                <button type="submit" class="btn" style="background-color: #16a34a;">Aprobar reporte</button>
            </form>

            {{-- Rechazar --}}
            <form method="POST" action="{{ route('profesor.reportes.rechazar', $reporte->id) }}"
                  onsubmit="return confirm('¿Rechazar este reporte? Esta acción indicará al alumno que su reporte fue rechazado.')">
                @csrf
                <button type="submit" class="btn btn-danger">Rechazar reporte</button>
            </form>
        </div>

        {{-- Corregir --}}
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <h4 style="margin-top: 0;">Solicitar correcciones</h4>
            <form method="POST" action="{{ route('profesor.reportes.corregir', $reporte->id) }}">
                @csrf
                <label for="correcciones">Correcciones que esperas del alumno</label>
                <textarea
                    id="correcciones"
                    name="correcciones"
                    rows="6"
                    style="max-width: 100%;"
                    placeholder="Escribe aquí todas las correcciones que necesita hacer el alumno en su reporte..."
                    required
                ></textarea>
                <button type="submit" class="btn" style="background-color: #d97706;"
                        onclick="return confirm('¿Enviar correcciones al alumno?')">
                    Enviar correcciones
                </button>
            </form>
        </div>
    </div>
@else
    <div class="card">
        <p class="muted">Este reporte tiene estado: <span class="badge">{{ $reporte->estado }}</span> y no puede ser revisado.</p>
    </div>
@endif
@endsection
