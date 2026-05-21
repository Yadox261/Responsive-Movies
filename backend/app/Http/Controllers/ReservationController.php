<?php

namespace App\Http\Controllers;

use App\Mail\ReservationMail;
use App\Models\Reservation;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Clase ReservationController
 * ----------------------------
 * Este controlador maneja la API para crear reservaciones desde el frontend (sitio del cliente).
 * Implementa validación estricta, almacenamiento en base de datos, generación dinámica de boletos
 * en formato PDF, envío de correos electrónicos SMTP y notificaciones automatizadas por WhatsApp.
 */
class ReservationController extends Controller
{
    /**
     * Guarda una nueva reservación en la base de datos y despacha las notificaciones.
     * 
     * @param Request $request Contiene los datos enviados desde el formulario AJAX del cliente.
     */
    public function store(Request $request)
    {
        /**
         * PASO 1: Validación Estricta en el Backend
         * ------------------------------------------
         * Evita el ingreso de datos basura o maliciosos en la base de datos de MySQL.
         */
        $validated = $request->validate([
            'movie_id'    => 'required|exists:movies,id',
            'schedule_id' => 'required|exists:schedules,id',
            'name'        => 'required|string|min:3|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|min:7|max:20',
            'seats'       => 'required|integer|min:1|max:10',
        ]);

        try {
            /**
             * PASO 2: Creación del Registro de Reservación
             */
            $reservation = Reservation::create($validated);
            
            // Carga eager-loading de relaciones de base de datos
            $reservation->load(['movie', 'schedule']);

            /**
             * PASO 3: Generación del Boleto Digital en PDF
             */
            $pdfContent = null;
            try {
                $pdf = Pdf::loadView('pdfs.reservation_ticket', compact('reservation'));
                $pdfContent = $pdf->output();
            } catch (\Exception $e) {
                Log::error('[ReservationController] Error al generar boleto PDF: ' . $e->getMessage());
            }

            /**
             * PASO 4: Envío del Correo de Confirmación vía SMTP
             */
            if ($pdfContent && $reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new ReservationMail($reservation, $pdfContent));
                    Log::info("[ReservationController] Correo de confirmación enviado a {$reservation->email} para la reserva #{$reservation->id}.");
                } catch (\Exception $e) {
                    Log::error('[ReservationController] Error al enviar correo de reservación: ' . $e->getMessage());
                }
            }

            /**
             * PASO 5: Notificación Reactiva por WhatsApp (UltraMsg API)
             */
            if ($reservation->phone) {
                try {
                    $movieTitle = $reservation->movie->title ?? 'una película';
                    $day        = $reservation->schedule->day ?? 'N/D';
                    $time       = $reservation->schedule->time ?? 'N/D';
                    $room       = $reservation->schedule->room ?? 'N/D';
                    $format     = $reservation->schedule->format ?? 'N/D';
                    
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

            /**
             * RETORNO DE LA RESPUESTA API (JSON)
             */
            return response()->json([
                'success' => true,
                'message' => '¡Reservación realizada con éxito!',
                'data'    => [
                    'id'    => $reservation->id,
                    'name'  => $reservation->name,
                    'movie' => $reservation->movie->title,
                    'day'   => $reservation->schedule->day,
                    'time'  => $reservation->schedule->time,
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
