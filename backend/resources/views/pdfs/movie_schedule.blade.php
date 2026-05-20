<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de Horario - Word of the Movies</title>
    <style>
        /* === ESTILOS BASE === */
        body {
            font-family: Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0a0a0f;
            color: #e8e8f0;
        }

        /* === CONTENEDOR PRINCIPAL === */
        .ticket {
            max-width: 520px;
            margin: 20px auto;
            background-color: #0d0d1a;
            border: 1px solid #2a1a2e;
            border-radius: 16px;
            overflow: hidden;
        }

        /* === HEADER CON GRADIENTE ROJO NEÓN === */
        .ticket-header {
            background: #c0392b;
            padding: 30px 25px 20px;
            text-align: center;
            position: relative;
        }

        .ticket-header .logo-text {
            font-size: 10px;
            letter-spacing: 6px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            margin: 0 0 6px;
        }

        .ticket-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .ticket-header .subtitle {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            margin: 8px 0 0;
            letter-spacing: 1px;
        }

        /* === SEPARADOR DE ENTRADA (PERFORADO) === */
        .perforation {
            display: flex;
            align-items: center;
            background-color: #0d0d1a;
        }

        .perf-circle-left {
            width: 20px;
            height: 20px;
            background-color: #0a0a0f;
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: -10px;
        }

        .perf-line {
            flex: 1;
            border-top: 2px dashed #2a1a2e;
            margin: 0 10px;
        }

        .perf-circle-right {
            width: 20px;
            height: 20px;
            background-color: #0a0a0f;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: -10px;
        }

        /* === CUERPO DEL TICKET === */
        .ticket-body {
            padding: 25px 30px;
        }

        .movie-title-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .movie-title-section .label-small {
            font-size: 9px;
            letter-spacing: 3px;
            color: #c0392b;
            text-transform: uppercase;
        }

        .movie-title-section .movie-name {
            font-size: 20px;
            font-weight: 900;
            color: #ffffff;
            margin: 4px 0 0;
            line-height: 1.2;
        }

        .movie-title-section .genre-badge {
            display: inline-block;
            margin-top: 6px;
            background-color: rgba(192, 57, 43, 0.2);
            border: 1px solid rgba(192, 57, 43, 0.4);
            color: #e74c3c;
            font-size: 10px;
            letter-spacing: 2px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* === CUADRÍCULA DE DATOS === */
        .info-grid {
            display: table;
            width: 100%;
            border-spacing: 0;
            margin: 18px 0;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            vertical-align: top;
        }

        .info-cell:first-child {
            padding-right: 15px;
        }

        .info-cell .cell-label {
            font-size: 9px;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-cell .cell-value {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
        }

        .info-cell .cell-value.highlight {
            color: #e74c3c;
            font-size: 18px;
        }

        /* === CÓDIGO DE BARRAS VISUAL === */
        .barcode-section {
            text-align: center;
            padding: 16px 0 0;
        }

        .barcode-bars {
            display: inline-block;
            height: 40px;
            font-size: 0;
            letter-spacing: 1px;
        }

        .barcode-text {
            font-size: 9px;
            letter-spacing: 4px;
            color: rgba(255,255,255,0.2);
            margin-top: 6px;
        }

        /* === FOOTER DEL TICKET === */
        .ticket-footer {
            background-color: rgba(192, 57, 43, 0.08);
            border-top: 1px dashed rgba(192, 57, 43, 0.2);
            padding: 14px 20px;
            text-align: center;
        }

        .ticket-footer p {
            margin: 0;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.3);
            line-height: 1.6;
        }

        /* === BADGE ESTRENO === */
        .premiere-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b35, #c0392b);
            color: #fff;
            font-size: 9px;
            letter-spacing: 2px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<div class="ticket">

    <!-- HEADER -->
    <div class="ticket-header">
        <p class="logo-text">Word of the Movies</p>
        <h1>Comprobante de Horario</h1>
        <p class="subtitle">Generado el {{ \Carbon\Carbon::now()->format('d \d\e F, Y \a \l\a\s H:i') }}</p>
    </div>

    <!-- PERFORACIÓN -->
    <div class="perforation">
        <div class="perf-circle-left"></div>
        <div class="perf-line"></div>
        <div class="perf-circle-right"></div>
    </div>

    <!-- CUERPO -->
    <div class="ticket-body">

        <!-- TÍTULO DE LA PELÍCULA -->
        <div class="movie-title-section">
            <div class="label-small">Película</div>
            <div class="movie-name">{{ $schedule->movie->title ?? 'Sin título' }}</div>
            @if($schedule->movie?->genre)
                <span class="genre-badge">{{ $schedule->movie->genre }}</span>
            @endif
            @if($schedule->movie?->is_premiere)
                <br><span class="premiere-badge">★ Estreno</span>
            @endif
        </div>

        <!-- DATOS DE LA FUNCIÓN -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="cell-label">Día</div>
                    <div class="cell-value">{{ $schedule->day }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Hora de Inicio</div>
                    <div class="cell-value highlight">{{ $schedule->time }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="cell-label">Sala</div>
                    <div class="cell-value">{{ $schedule->room }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Formato</div>
                    <div class="cell-value">{{ $schedule->format }}</div>
                </div>
            </div>
            @if($schedule->movie?->duration)
            <div class="info-row">
                <div class="info-cell">
                    <div class="cell-label">Duración</div>
                    <div class="cell-value">{{ $schedule->movie->duration }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Director</div>
                    <div class="cell-value">{{ $schedule->movie->director ?? 'N/D' }}</div>
                </div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-cell">
                    <div class="cell-label">ID de Horario</div>
                    <div class="cell-value">#{{ str_pad($schedule->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Estado</div>
                    <div class="cell-value" style="color: #2ecc71;">● ACTIVO</div>
                </div>
            </div>
        </div>

        <!-- CÓDIGO DE BARRAS VISUAL -->
        <div class="barcode-section">
            <div class="barcode-text">{{ strtoupper(str_pad($schedule->id, 12, '0', STR_PAD_LEFT)) }}-WOM-{{ strtoupper(substr(md5($schedule->id), 0, 6)) }}</div>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="ticket-footer">
        <p>Este comprobante fue generado automáticamente por Word of the Movies.<br>
        Favor de presentarlo al personal de sala al momento del ingreso.<br>
        © {{ date('Y') }} Word of the Movies. Todos los derechos reservados.</p>
    </div>

</div>

</body>
</html>
