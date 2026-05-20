<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nuevo Horario Confirmado - Word of the Movies</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0f;
            margin: 0;
            padding: 20px 0;
            color: #ccc;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #0d0d1a;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #1e0a14;
        }
        .header {
            background: #c0392b;
            padding: 35px 30px 28px;
            text-align: center;
        }
        .header .brand {
            font-size: 11px;
            letter-spacing: 5px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            margin: 0 0 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 1px;
        }
        .header .tagline {
            margin: 8px 0 0;
            font-size: 12px;
            color: rgba(255,255,255,0.65);
        }
        .body {
            padding: 30px 35px;
        }
        .greeting {
            font-size: 17px;
            font-weight: 600;
            color: #ffffff;
            margin: 0 0 10px;
        }
        .intro {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            margin: 0 0 25px;
        }
        .schedule-card {
            background-color: rgba(255,255,255,0.04);
            border: 1px solid rgba(192, 57, 43, 0.25);
            border-left: 4px solid #c0392b;
            border-radius: 10px;
            padding: 20px 22px;
            margin-bottom: 25px;
        }
        .schedule-card .movie-title {
            font-size: 19px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 4px;
        }
        .schedule-card .genre {
            font-size: 11px;
            letter-spacing: 2px;
            color: #e74c3c;
            text-transform: uppercase;
            margin: 0 0 16px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            min-width: 100px;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
        }
        .detail-value.big {
            font-size: 16px;
            color: #e74c3c;
        }
        .pdf-notice {
            background-color: rgba(192, 57, 43, 0.1);
            border: 1px solid rgba(192, 57, 43, 0.2);
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 25px;
        }
        .pdf-notice strong {
            color: #e74c3c;
        }
        .footer {
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 18px 35px;
            text-align: center;
        }
        .footer p {
            margin: 0;
            font-size: 11px;
            color: rgba(255,255,255,0.2);
            line-height: 1.7;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <p class="brand">Word of the Movies</p>
        <h1>🎬 Horario Confirmado</h1>
        <p class="tagline">Se ha registrado una nueva función en el sistema</p>
    </div>

    <!-- CUERPO -->
    <div class="body">
        <p class="greeting">¡Horario publicado con éxito!</p>
        <p class="intro">
            Se ha registrado correctamente el siguiente horario en la cartelera.
            Encontrarás el comprobante completo en PDF adjunto a este correo.
        </p>

        <!-- TARJETA DEL HORARIO -->
        <div class="schedule-card">
            <div class="movie-title">{{ $schedule->movie->title ?? 'Sin título' }}</div>
            @if($schedule->movie?->genre)
            <div class="genre">{{ $schedule->movie->genre }}</div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Día</span>
                <span class="detail-value">{{ $schedule->day }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Hora</span>
                <span class="detail-value big">{{ $schedule->time }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Sala</span>
                <span class="detail-value">{{ $schedule->room }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Formato</span>
                <span class="detail-value">{{ $schedule->format }}</span>
            </div>
            @if($schedule->movie?->duration)
            <div class="detail-row">
                <span class="detail-label">Duración</span>
                <span class="detail-value">{{ $schedule->movie->duration }}</span>
            </div>
            @endif
            @if($schedule->movie?->is_premiere)
            <div class="detail-row">
                <span class="detail-label">Tipo</span>
                <span class="detail-value" style="color:#f39c12;">★ Estreno</span>
            </div>
            @endif
        </div>

        <!-- AVISO PDF ADJUNTO -->
        <div class="pdf-notice">
            📄 <strong>Comprobante PDF adjunto:</strong> Hemos generado y adjuntado un comprobante oficial
            en formato PDF tipo "entrada de cine" con todos los detalles de esta función.
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>
            Este correo fue generado automáticamente por Word of the Movies.<br>
            © {{ date('Y') }} Word of the Movies. Todos los derechos reservados.
        </p>
    </div>

</div>
</body>
</html>
