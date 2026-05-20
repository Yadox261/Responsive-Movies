<?php

namespace App\Observers;

use App\Mail\MovieScheduleMail;
use App\Models\Schedule;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ScheduleObserver
{
    /**
     * Handle the Schedule "created" event.
     * Se dispara automáticamente al registrar un horario nuevo desde el panel.
     */
    public function created(Schedule $schedule): void
    {
        // Cargar la relación con la película
        $schedule->load('movie');

        // 1. Generar el PDF de la "Entrada de Cine"
        $pdfContent = null;
        try {
            $pdf        = Pdf::loadView('pdfs.movie_schedule', compact('schedule'));
            $pdfContent = $pdf->output();
        } catch (\Exception $e) {
            Log::error('[ScheduleObserver] Error al generar el PDF: ' . $e->getMessage());
        }

        // 2. Determinar el destinatario del correo inmediato (usuario activo o admin de respaldo)
        $recipient = Auth::user();

        if (!$recipient) {
            // Respaldo: primer usuario con rol Admin
            $recipient = \App\Models\User::where('role_id', 1)->first();
        }

        // 3. Enviar correo con PDF adjunto
        if ($pdfContent && $recipient?->email) {
            try {
                Mail::to($recipient->email)->send(new MovieScheduleMail($schedule, $pdfContent));
                Log::info("[ScheduleObserver] Correo de confirmación enviado a {$recipient->email} para horario #{$schedule->id}.");
            } catch (\Exception $e) {
                Log::error('[ScheduleObserver] Error al enviar correo: ' . $e->getMessage());
            }
        }

        // 4. Enviar WhatsApp al usuario activo si tiene número registrado
        try {
            if ($recipient && $recipient->phone_number) {
                $fullPhone  = ($recipient->country_code ?? '+52') . $recipient->phone_number;
                $movieTitle = $schedule->movie->title ?? 'una película';

                $message = "🎬 *Word of the Movies*\n\n"
                    . "Se ha registrado un nuevo horario:\n"
                    . "📽️ *Película:* {$movieTitle}\n"
                    . "📅 *Día:* {$schedule->day}\n"
                    . "⏰ *Hora:* {$schedule->time}\n"
                    . "🏛️ *Sala:* {$schedule->room}\n"
                    . "🎞️ *Formato:* {$schedule->format}\n\n"
                    . "El comprobante PDF ha sido enviado a tu correo.";

                $whatsapp = new WhatsAppService();
                $whatsapp->sendMessage($fullPhone, $message);
            }
        } catch (\Exception $e) {
            Log::error('[ScheduleObserver] Error al enviar WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Schedule "updated" event.
     */
    public function updated(Schedule $schedule): void
    {
        //
    }

    /**
     * Handle the Schedule "deleted" event.
     */
    public function deleted(Schedule $schedule): void
    {
        //
    }
}
