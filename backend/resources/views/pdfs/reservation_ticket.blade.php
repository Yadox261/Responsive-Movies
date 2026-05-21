<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de Reservación - Word of the Movies</title>
    <style>
        /**
         * === REGLA IMPORTANTE PARA EL PDF (DomPDF) ===
         * DomPDF interpreta HTML y CSS para compilar el PDF. 
         * No soporta grids modernos de CSS (Flexbox de forma completa o CSS Grid). 
         * Por ello, para estructurar las columnas de datos utilizamos la propiedad 'display: table' 
         * y 'display: table-cell', lo cual emula tablas invisibles ultra-compatibles con el motor PDF.
         */

        /* === ESTILOS BASE === */
        body {
            font-family: Helvetica, Arial, sans-serif; /* Tipografía estándar compatible con DomPDF sin añadir fuentes pesadas */
            margin: 0;
            padding: 0;
            background-color: #0a0a0f; /* Fondo oscuro elegante */
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
            background: #ff3333; /* Fondo corporativo neón */
            padding: 30px 25px 20px;
            text-align: center;
            position: relative;
        }

        .ticket-header .logo-text {
            font-size: 10px;
            letter-spacing: 6px;
            color: rgba(255,255,255,0.7);
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
            color: rgba(255,255,255,0.8);
            margin: 8px 0 0;
            letter-spacing: 1px;
        }

        /* === SEPARADOR DE ENTRADA (PERFORADO VIRTUAL) ===
         * Emula el corte de cupón físico de un boleto real de cine.
         * Utiliza círculos redondeados a los extremos del boleto para dar el efecto de corte.
         */
        .perforation {
            display: flex;
            align-items: center;
            background-color: #0d0d1a;
        }

        /* Círculo izquierdo de perforación */
        .perf-circle-left {
            width: 20px;
            height: 20px;
            background-color: #0a0a0f; /* Del mismo color de fondo del body para simular transparencia */
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: -10px;
        }

        /* Línea punteada de rasgado */
        .perf-line {
            flex: 1;
            border-top: 2px dashed #2a1a2e;
            margin: 0 10px;
        }

        /* Círculo derecho de perforación */
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
            color: #ff3333;
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
            background-color: rgba(255, 51, 51, 0.2);
            border: 1px solid rgba(255, 51, 51, 0.4);
            color: #ff4d4d;
            font-size: 10px;
            letter-spacing: 2px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* === CUADRÍCULA DE DATOS COMPATIBLE CON PDF ===
         * Emula un grid responsivo usando tablas CSS compatibles con DomPDF.
         */
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
            color: #ff3333;
            font-size: 18px;
        }

        /* === CÓDIGO DE BARRAS VISUAL === */
        .barcode-section {
            text-align: center;
            padding: 16px 0 0;
        }

        .barcode-text {
            font-size: 9px;
            letter-spacing: 4px;
            color: rgba(255,255,255,0.25);
            margin-top: 6px;
        }

        /* === FOOTER DEL TICKET === */
        .ticket-footer {
            background-color: rgba(255, 51, 51, 0.08);
            border-top: 1px dashed rgba(255, 51, 51, 0.2);
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
            background: linear-gradient(135deg, #ff6b35, #ff3333);
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

    <!-- HEADER: Muestra el logo y el ID de reservación formateado con ceros a la izquierda (ej: #000005) -->
    <div class="ticket-header">
        <p class="logo-text">Word of the Movies</p>
        <h1>Boleto de Entrada</h1>
        <p class="subtitle">Reservación #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <!-- PERFORACIÓN ESTÉTICA -->
    <div class="perforation">
        <div class="perf-circle-left"></div>
        <div class="perf-line"></div>
        <div class="perf-circle-right"></div>
    </div>

    <!-- CUERPO DE DATOS DINÁMICOS -->
    <div class="ticket-body">

        <!-- TÍTULO DE LA PELÍCULA E INDICADORES (RELACIÓN MOVIE) -->
        <div class="movie-title-section">
            <div class="label-small">Película</div>
            <div class="movie-name">{{ $reservation->movie->title ?? 'Sin título' }}</div>
            @if($reservation->movie?->genre)
                <span class="genre-badge">{{ $reservation->movie->genre }}</span>
            @endif
            @if($reservation->movie?->is_premiere)
                <br><span class="premiere-badge">★ Estreno</span>
            @endif
        </div>

        <!-- CUADRÍCULA DE DETALLES DE LA RESERVA (EAGER-LOADING) -->
        <div class="info-grid">
            <div class="info-row">
                <!-- Fila 1: Nombre del cliente y cantidad de asientos solicitados -->
                <div class="info-cell">
                    <div class="cell-label">Cliente</div>
                    <div class="cell-value">{{ $reservation->name }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Cantidad</div>
                    <div class="cell-value highlight">{{ $reservation->seats }} Asiento(s)</div>
                </div>
            </div>
            <div class="info-row">
                <!-- Fila 2: Datos de fecha y hora obtenidos de la relación 'schedule' -->
                <div class="info-cell">
                    <div class="cell-label">Día</div>
                    <div class="cell-value">{{ $reservation->schedule->day ?? 'N/D' }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Hora</div>
                    <div class="cell-value">{{ $reservation->schedule->time ?? 'N/D' }}</div>
                </div>
            </div>
            <div class="info-row">
                <!-- Fila 3: Sala de proyección y formato tecnológico (2D, 3D, IMAX) -->
                <div class="info-cell">
                    <div class="cell-label">Sala</div>
                    <div class="cell-value">{{ $reservation->schedule->room ?? 'N/D' }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Formato</div>
                    <div class="cell-value">{{ $reservation->schedule->format ?? 'N/D' }}</div>
                </div>
            </div>
            <div class="info-row">
                <!-- Fila 4: Correo electrónico y teléfono de contacto para auditorías rápidas -->
                <div class="info-cell">
                    <div class="cell-label">Email</div>
                    <div class="cell-value" style="font-size: 11px;">{{ $reservation->email }}</div>
                </div>
                <div class="info-cell">
                    <div class="cell-label">Teléfono</div>
                    <div class="cell-value">{{ $reservation->phone }}</div>
                </div>
            </div>
        </div>

        <!-- CÓDIGO DE BARRAS NUMÉRICO PROGRAMADO
         * Genera un código de seguridad único mezclando el ID de reservación 
         * y un hash MD5 corto obtenido dinámicamente, asegurando autenticidad en la entrada.
         */ -->
        <div class="barcode-section">
            <div class="barcode-text">RES-{{ str_pad($reservation->id, 8, '0', STR_PAD_LEFT) }}-WOM-{{ strtoupper(substr(md5($reservation->id), 0, 6)) }}</div>
        </div>

    </div>

    <!-- PIE DEL COMPROBANTE -->
    <div class="ticket-footer">
        <p>Este boleto es un comprobante de reservación.<br>
        Favor de presentarlo digitalizado al personal de taquilla o ingreso.<br>
        © {{ date('Y') }} Word of the Movies. Todos los derechos reservados.</p>
    </div>

</div>

</body>
</html>
