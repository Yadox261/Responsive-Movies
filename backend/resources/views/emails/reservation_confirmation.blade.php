<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Confirmación de Reservación - Word of the Movies</title>
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
            border: 1px solid #2a1a2e;
        }
        .header {
            background: linear-gradient(135deg, #ff3333, #990000);
            padding: 35px 30px 28px;
            text-align: center;
        }
        .header .brand {
            font-size: 11px;
            letter-spacing: 5px;
            color: rgba(255,255,255,0.7);
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
            color: rgba(255,255,255,0.75);
        }
        .body {
            padding: 30px 35px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 10px;
        }
        .intro {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            margin: 0 0 25px;
        }
        .ticket-card {
            background-color: rgba(255,255,255,0.03);
            border: 1px solid rgba(255, 51, 51, 0.2);
            border-left: 4px solid #ff3333;
            border-radius: 10px;
            padding: 20px 22px;
            margin-bottom: 25px;
        }
        .ticket-card .movie-title {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 4px;
        }
        .ticket-card .genre {
            font-size: 11px;
            letter-spacing: 2px;
            color: #ff3333;
            text-transform: uppercase;
            margin: 0 0 16px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            min-width: 120px;
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
            color: #ff3333;
        }
        .pdf-notice {
            background-color: rgba(255, 51, 51, 0.08);
            border: 1px solid rgba(255, 51, 51, 0.15);
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 25px;
        }
        .pdf-notice strong {
            color: #ff3333;
        }
        .footer {
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 18px 35px;
            text-align: center;
        }
        .footer p {
            margin: 0;
            font-size: 11px;
            color: rgba(255,255,255,0.25);
            line-height: 1.7;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <p class="brand">Word of the Movies</p>
        <h1>🎟️ Reservación Confirmada</h1>
        <p class="tagline">¡Tu lugar en la sala ya está asegurado!</p>
    </div>

    <!-- CUERPO -->
    <div class="body">
        <p class="greeting">¡Hola, {{ $reservation->name }}!</p>
        <p class="intro">
            Hemos registrado tu reservación con éxito en nuestro sistema. 
            A continuación, encontrarás los detalles de tu entrada de cine. 
            Tu boleto digital en formato PDF ha sido adjuntado a este correo.
        </p>

        <!-- TARJETA DEL BOLETO -->
        <div class="ticket-card">
            <div class="movie-title">{{ $reservation->movie->title ?? 'Sin título' }}</div>
            @if($reservation->movie?->genre)
            <div class="genre">{{ $reservation->movie->genre }}</div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Código Reserva</span>
                <span class="detail-value" style="color: #ff3333; font-weight: bold;">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Día</span>
                <span class="detail-value">{{ $reservation->schedule->day ?? 'N/D' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Horario</span>
                <span class="detail-value big">{{ $reservation->schedule->time ?? 'N/D' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Sala</span>
                <span class="detail-value">{{ $reservation->schedule->room ?? 'N/D' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Formato</span>
                <span class="detail-value">{{ $reservation->schedule->format ?? 'N/D' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Boletos</span>
                <span class="detail-value" style="color: #2ecc71;">{{ $reservation->seats }} asiento(s)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Cliente</span>
                <span class="detail-value">{{ $reservation->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">WhatsApp</span>
                <span class="detail-value">{{ $reservation->phone }}</span>
            </div>
        </div>

        <!-- AVISO PDF ADJUNTO -->
        <div class="pdf-notice">
            📄 <strong>Boleto PDF Adjunto:</strong> Hemos generado tu entrada digital en PDF. 
            Favor de descargarla y presentarla en tu celular al momento de ingresar a la sala para su escaneo.
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>
            Este correo fue generado de manera automática por el sistema de reservaciones de Word of the Movies.<br>
            © {{ date('Y') }} Word of the Movies. Todos los derechos reservados.
        </p>
    </div>

</div>
</body>
</html>
