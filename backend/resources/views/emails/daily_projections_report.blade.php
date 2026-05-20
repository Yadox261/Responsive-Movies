<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cartelera del Día — Word of the Movies</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0f;
            margin: 0;
            padding: 20px 0;
            color: #ccc;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #0d0d1a;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #1a0a14;
        }
        /* ── HEADER ─────────────────────────────── */
        .header {
            background: #c0392b;
            padding: 36px 30px 28px;
            text-align: center;
        }
        .header .brand {
            font-size: 10px;
            letter-spacing: 6px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            margin: 0 0 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 1px;
        }
        .header .subtitle {
            margin: 8px 0 0;
            font-size: 13px;
            color: rgba(255,255,255,0.65);
        }
        /* ── CUERPO ──────────────────────────────── */
        .body {
            padding: 30px 35px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 8px;
        }
        .intro {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            line-height: 1.7;
            margin: 0 0 28px;
        }
        /* ── RESUMEN NUMÉRICO ────────────────────── */
        .summary-strip {
            display: flex;
            gap: 0;
            margin-bottom: 28px;
            border: 1px solid rgba(192,57,43,0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        .summary-item {
            flex: 1;
            text-align: center;
            padding: 16px 10px;
            background: rgba(255,255,255,0.03);
            border-right: 1px solid rgba(192,57,43,0.15);
        }
        .summary-item:last-child { border-right: none; }
        .summary-item .s-number {
            font-size: 26px;
            font-weight: 900;
            color: #e74c3c;
            line-height: 1;
        }
        .summary-item .s-label {
            font-size: 10px;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            margin-top: 4px;
        }
        /* ── TABLA DE PROYECCIONES ───────────────── */
        .section-title {
            font-size: 11px;
            letter-spacing: 3px;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            margin: 0 0 14px;
        }
        table.schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.schedule-table thead tr {
            background: rgba(192,57,43,0.15);
        }
        table.schedule-table thead th {
            padding: 10px 12px;
            font-size: 10px;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            text-align: left;
            border-bottom: 1px solid rgba(192,57,43,0.2);
        }
        table.schedule-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        table.schedule-table tbody tr:last-child {
            border-bottom: none;
        }
        table.schedule-table tbody td {
            padding: 13px 12px;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
            vertical-align: middle;
        }
        .td-movie {
            font-weight: 700;
            color: #ffffff;
        }
        .td-time {
            font-weight: 800;
            font-size: 15px;
            color: #e74c3c;
        }
        .td-room {
            color: rgba(255,255,255,0.6);
        }
        .td-format {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
        }
        .premiere-tag {
            display: inline-block;
            background: rgba(243,156,18,0.15);
            color: #f39c12;
            font-size: 9px;
            letter-spacing: 1px;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            margin-left: 5px;
            vertical-align: middle;
        }
        /* ── FOOTER ──────────────────────────────── */
        .footer {
            border-top: 1px solid rgba(255,255,255,0.04);
            padding: 20px 35px;
            text-align: center;
        }
        .footer p {
            margin: 0;
            font-size: 11px;
            color: rgba(255,255,255,0.18);
            line-height: 1.8;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <p class="brand">Word of the Movies</p>
        <h1>🎬 Cartelera del {{ $dayName }}</h1>
        <p class="subtitle">{{ \Carbon\Carbon::now()->format('d \d\e F \d\e Y') }} &mdash; Reporte Matutino</p>
    </div>

    <!-- CUERPO -->
    <div class="body">

        <p class="greeting">Buenos días, {{ $recipientName }} 👋</p>
        <p class="intro">
            A continuación encontrarás el resumen de las funciones programadas para el día de hoy.
            Este reporte es generado automáticamente cada mañana para el equipo de administración y editores.
        </p>

        @if($schedules->isEmpty())
            <!-- SIN PROYECCIONES -->
            <div style="text-align:center; padding: 40px 20px; color: rgba(255,255,255,0.3);">
                <div style="font-size: 40px; margin-bottom: 12px;">🎭</div>
                <div style="font-size: 15px; font-weight: 600; color: rgba(255,255,255,0.5);">Sin funciones programadas hoy</div>
                <div style="font-size: 12px; margin-top: 6px;">No hay proyecciones registradas para el {{ $dayName }}.</div>
            </div>
        @else
            <!-- RESUMEN NUMÉRICO -->
            @php
                $totalSchedules = $schedules->count();
                $uniqueMovies   = $schedules->pluck('movie_id')->unique()->count();
                $uniqueRooms    = $schedules->pluck('room')->unique()->count();
                $premieres      = $schedules->filter(fn($s) => $s->movie?->is_premiere)->count();
            @endphp

            <div class="summary-strip">
                <div class="summary-item">
                    <div class="s-number">{{ $totalSchedules }}</div>
                    <div class="s-label">Funciones</div>
                </div>
                <div class="summary-item">
                    <div class="s-number">{{ $uniqueMovies }}</div>
                    <div class="s-label">Películas</div>
                </div>
                <div class="summary-item">
                    <div class="s-number">{{ $uniqueRooms }}</div>
                    <div class="s-label">Salas</div>
                </div>
                @if($premieres > 0)
                <div class="summary-item">
                    <div class="s-number" style="color:#f39c12;">{{ $premieres }}</div>
                    <div class="s-label">Estrenos</div>
                </div>
                @endif
            </div>

            <!-- TABLA DE PROYECCIONES -->
            <div class="section-title">Detalle de Proyecciones</div>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Película</th>
                        <th>Hora</th>
                        <th>Sala</th>
                        <th>Formato</th>
                        <th>Duración</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules->sortBy('time') as $schedule)
                    <tr>
                        <td class="td-movie">
                            {{ $schedule->movie->title ?? 'Sin título' }}
                            @if($schedule->movie?->is_premiere)
                                <span class="premiere-tag">★ Estreno</span>
                            @endif
                        </td>
                        <td class="td-time">{{ $schedule->time }}</td>
                        <td class="td-room">{{ $schedule->room }}</td>
                        <td class="td-format">{{ $schedule->format }}</td>
                        <td class="td-format">{{ $schedule->movie?->duration ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>
            Este correo es enviado automáticamente cada mañana por Word of the Movies.<br>
            Está dirigido únicamente al equipo de administradores y editores.<br>
            © {{ date('Y') }} Word of the Movies. Todos los derechos reservados.
        </p>
    </div>

</div>
</body>
</html>
