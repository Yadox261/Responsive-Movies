<?php

namespace App\Console\Commands;

use App\Mail\DailyProjectionsReportMail;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyProjectionsReportCommand extends Command
{
    /**
     * Nombre y firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-projections-report';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Envía el reporte matutino de cartelera del día a todos los administradores y editores.';

    /**
     * Mapa de número de día (dayOfWeek) a nombre en español.
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
     * Ejecutar el comando.
     */
    public function handle(): void
    {
        $dayName   = $this->getDayName();
        $isWeekend = in_array(Carbon::now()->dayOfWeek, [0, 6]);

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  🎬 Word of the Movies — Reporte Matutino");
        $this->info("  Día: {$dayName} | " . Carbon::now()->format('d/m/Y H:i'));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        // 1. Obtener proyecciones para el día actual.
        //    Un Schedule puede tener day = 'Lunes', 'Fin de Semana', 'Lunes a Viernes' o 'Todos los días'.
        $schedules = Schedule::with('movie')
            ->where(function ($query) use ($dayName, $isWeekend) {
                $query->where('day', $dayName)                  // día exacto
                      ->orWhere('day', 'Todos los días')        // todos los días
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

        // 2. Obtener TODOS los administradores (role_id = 1) y editores (role_id = 2)
        $recipients = User::whereIn('role_id', [1, 2])->get();

        if ($recipients->isEmpty()) {
            $this->error("  ❌ No se encontraron administradores ni editores en el sistema.");
            return;
        }

        $this->info("  👥 Destinatarios: {$recipients->count()} (admins + editores)");
        $this->newLine();

        // 3. Enviar correo a cada destinatario con desfase de 15 segundos
        //    para no exceder el límite de la API de pruebas (Mailtrap).
        $sent  = 0;
        $total = $recipients->count();

        foreach ($recipients as $index => $user) {
            try {
                Mail::to($user->email)->send(
                    new DailyProjectionsReportMail($schedules, $dayName, $user->name)
                );

                $sent++;
                $role = $user->role_id === 1 ? 'Admin' : 'Editor';
                $this->info("  ✅ [{$sent}/{$total}] Correo enviado a {$user->name} ({$role}) — {$user->email}");

                Log::info("[DailyProjections] Reporte matutino enviado a {$user->email} ({$role}).");

                // ─── DESFASE ANTIBLOQUEO ───────────────────────────────────
                // Pausa de 15 segundos entre correos para respetar el límite
                // de la API de Mailtrap u otros proveedores de prueba.
                if ($index < $total - 1) {
                    $this->line("  ⏳ Esperando 15 segundos para evitar bloqueo de la API...");
                    sleep(15);
                }
                // ──────────────────────────────────────────────────────────

            } catch (\Exception $e) {
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
