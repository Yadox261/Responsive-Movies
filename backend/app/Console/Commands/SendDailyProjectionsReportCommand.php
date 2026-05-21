<?php

namespace App\Console\Commands;

use App\Mail\DailyProjectionsReportMail;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Clase SendDailyProjectionsReportCommand (Artisan Task Scheduling)
 * ------------------------------------------------------------------
 * Este comando representa el módulo de AUTOMATIZACIÓN (Cron Jobs / Tareas Programadas) de la rúbrica.
 * Se encarga de construir la cartelera de películas del día de hoy y enviarla por correo HTML
 * a todos los directores, administradores y editores a primera hora de la mañana.
 */
class SendDailyProjectionsReportCommand extends Command
{
    /**
     * Nombre y firma del comando de consola.
     * Es el disparador que se invoca en la terminal de comandos.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-projections-report';

    /**
     * Descripción descriptiva del comando en el menú 'php artisan list'.
     *
     * @var string
     */
    protected $description = 'Envía el reporte matutino de cartelera del día a todos los administradores y editores.';

    /**
     * getDayName
     * -----------
     * Método auxiliar de fecha. Obtiene el día de la semana actual utilizando Carbon
     * y lo mapea a su traducción al español. Sirve para filtrar de manera exacta los horarios.
     */
    private function getDayName(): string
    {
        $map = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        return $map[Carbon::now()->dayOfWeek] ?? 'Todos los días';
    }

    /**
     * Método Ejecutor handle()
     * ------------------------
     * Es el punto de entrada que ejecuta Laravel de manera automática cuando se activa el planificador.
     */
    public function handle(): void
    {
        $dayName   = $this->getDayName();
        // Evalúa si el día actual cae en Fin de Semana (Sábado = 6, Domingo = 0)
        $isWeekend = in_array(Carbon::now()->dayOfWeek, [0, 6]);

        // Pinta bordes estéticos en la consola si el usuario lo corre de forma manual
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  🎬 Word of the Movies — Reporte Matutino");
        $this->info("  Día: {$dayName} | " . Carbon::now()->format('d/m/Y H:i'));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        /**
         * PASO 1: Consulta de Proyecciones de Hoy
         * ----------------------------------------
         * Filtra la tabla 'schedules' relacionando el día actual:
         * - Coincidencia exacta (ej: 'Lunes')
         * - Todos los días ('Todos los días')
         * - Rangos asociativos ('Lunes a Viernes' si hoy es entre lunes y viernes; 'Fin de Semana' si es sábado/domingo).
         */
        $schedules = Schedule::with('movie')
            ->where(function ($query) use ($dayName, $isWeekend) {
                $query->where('day', $dayName)                  
                      ->orWhere('day', 'Todos los días')        
                      ->orWhere(function ($q) use ($dayName, $isWeekend) {
                            if (in_array($dayName, ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'])) {
                                $q->where('day', 'Lunes a Viernes');
                            }
                            if ($isWeekend) {
                                $q->orWhere('day', 'Fin de Semana');
                            }
                        });
            })
            ->orderBy('time')
            ->get();

        $count = $schedules->count();
        $this->info("  📋 Proyecciones encontradas para hoy: {$count}");

        if ($count === 0) {
            $this->warn("  ⚠️  No hay funciones programadas para hoy ({$dayName}). Se enviará el reporte vacío igualmente.");
        }

        /**
         * PASO 2: Obtener Destinatarios Administrativos
         * ----------------------------------------------
         * Filtra la tabla de usuarios buscando a todos los administradores (role_id = 1)
         * y a todos los editores (role_id = 2) autorizados en el sistema.
         */
        $recipients = User::whereIn('role_id', [1, 2])->get();

        if ($recipients->isEmpty()) {
            $this->error("  ❌ No se encontraron administradores ni editores en el sistema.");
            return;
        }

        $this->info("  👥 Destinatarios: {$recipients->count()} (admins + editores)");
        $this->newLine();

        /**
         * PASO 3: Bucle de Envío de Correos con Tasa de Seguridad (Rate-limiting)
         * ------------------------------------------------------------------------
         * Itera sobre cada usuario y despacha de forma asíncrona la clase mailable.
         * ALERTA DE SEGURIDAD (Esencial para la defensa):
         * Servidores de correo de prueba (como Mailtrap) o de producción limitan el número de correos
         * que se pueden procesar por segundo. Para evitar que la tarea falle o sea rechazada por spam,
         * aplicamos 'sleep(15)' pausando el hilo de ejecución durante 15 segundos entre cada entrega.
         */
        $sent  = 0;
        $total = $recipients->count();

        foreach ($recipients as $index => $user) {
            try {
                // Envío de correo a la dirección del destinatario
                Mail::to($user->email)->send(
                    new DailyProjectionsReportMail($schedules, $dayName, $user->name)
                );

                $sent++;
                $role = $user->role_id === 1 ? 'Admin' : 'Editor';
                $this->info("  ✅ [{$sent}/{$total}] Correo enviado a {$user->name} ({$role}) — {$user->email}");

                // Registra la auditoría en logs de Laravel
                Log::info("[DailyProjections] Reporte matutino enviado a {$user->email} ({$role}).");

                /**
                 * ─── RETARDO ANTIBLOQUEO (PAUSA INTELIGENTE) ───
                 * Si todavía quedan destinatarios pendientes en la cola, pausamos el script 15 segundos.
                 */
                if ($index < $total - 1) {
                    $this->line("  ⏳ Esperando 15 segundos para evitar bloqueo de la API...");
                    sleep(15);
                }
                // ───────────────────────────────────────────────

            } catch (\Exception $e) {
                // Si la entrega a un usuario falla, captura el error, lo registra y continúa con el siguiente sin colapsar la cola
                $this->error("  ❌ Error al enviar a {$user->email}: " . $e->getMessage());
                Log::error("[DailyProjections] Error al enviar reporte a {$user->email}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  ✔ Proceso completado: {$sent}/{$total} correos enviados.");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        Log::info("[DailyProjections] Reporte matutino completado. {$sent}/{$total} correos enviados. Día: {$dayName}.");
    }
}
