<?php

namespace App\Http\Controllers;

use App\Mail\ReservationMail;
use App\Models\Reservation;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar la solicitud
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'schedule_id' => 'required|exists:schedules,id',
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:7|max:20',
            'seats' => 'required|integer|min:1|max:10',
        ]);

        try {
            // 2. Crear la reservación
            $reservation = Reservation::create($validated);
            
            // Cargar relaciones
            $reservation->load(['movie', 'schedule']);

            // 3. Generar el PDF del Boleto
            $pdfContent = null;
            try {
                $pdf = Pdf::loadView('pdfs.reservation_ticket', compact('reservation'));
                $pdfContent = $pdf->output();
            } catch (\Exception $e) {
                Log::error('[ReservationController] Error al generar boleto PDF: ' . $e->getMessage());
            }

            // 4. Enviar Correo de Confirmación con PDF
            if ($pdfContent && $reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new ReservationMail($reservation, $pdfContent));
                    Log::info("[ReservationController] Correo de confirmación enviado a {$reservation->email} para la reserva #{$reservation->id}.");
                } catch (\Exception $e) {
                    Log::error('[ReservationController] Error al enviar correo de reservación: ' . $e->getMessage());
                }
            }

            // 5. Enviar mensaje de WhatsApp
            if ($reservation->phone) {
                try {
                    $movieTitle = $reservation->movie->title ?? 'una película';
                    $day = $reservation->schedule->day ?? 'N/D';
                    $time = $reservation->schedule->time ?? 'N/D';
                    $room = $reservation->schedule->room ?? 'N/D';
                    $format = $reservation->schedule->format ?? 'N/D';
                    
                    $message = "🎟️ *Word of the Movies - Reservación Confirmada*\n\n"
                        . "¡Hola *{$reservation->name}*! Tu reservación para *{$movieTitle}* se ha guardado exitosamente:\n\n"
                        . "📽️ *Película:* {$movieTitle}\n"
                        . "📅 *Día:* {$day}\n"
                        . "⏰ *Hora:* {$time}\n"
                        . "🏛️ *Sala:* {$room}\n"
                        . "🎞️ *Formato:* {$format}\n"
                        . "👥 *Cantidad:* {$reservation->seats} boleto(s)\n\n"
                        . "Hemos enviado un correo de confirmación con tu boleto PDF adjunto a: {$reservation->email}.\n\n"
                        . "¡Te esperamos! Disfruta la función 🍿";

                    $whatsapp = new WhatsAppService();
                    $whatsapp->sendMessage($reservation->phone, $message);
                } catch (\Exception $e) {
                    Log::error('[ReservationController] Error al enviar WhatsApp de reservación: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => '¡Reservación realizada con éxito!',
                'data' => [
                    'id' => $reservation->id,
                    'name' => $reservation->name,
                    'movie' => $reservation->movie->title,
                    'day' => $reservation->schedule->day,
                    'time' => $reservation->schedule->time,
                    'seats' => $reservation->seats,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('[ReservationController] Error general al procesar reservación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar la reservación. Por favor intenta de nuevo.'
            ], 500);
        }
    }
}
