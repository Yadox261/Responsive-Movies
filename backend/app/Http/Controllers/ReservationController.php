<?php

namespace App\Http\Controllers;

// Importación de las clases y mailables necesarios para el funcionamiento del controlador
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
         * - 'movie_id' y 'schedule_id' deben existir en sus respectivas tablas ('exists:').
         * - 'seats' (boletos) debe ser un entero con un rango estrictamente entre 1 y 10.
         * - 'email' debe cumplir con el patrón formal de correo electrónico.
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
             * ---------------------------------------------
             * Inserta la reservación en la tabla 'reservations' utilizando asignación masiva ($fillable).
             * Posteriormente, carga las relaciones ('movie' y 'schedule') para disponer de la
             * información detallada de la película y el horario para el PDF y las notificaciones.
             */
            $reservation = Reservation::create($validated);
            
            // Carga eager-loading de relaciones de base de datos para evitar consultas repetitivas (problema N+1)
            $reservation->load(['movie', 'schedule']);

            /**
             * PASO 3: Generación del Boleto Digital en PDF
             * ---------------------------------------------
             * Utiliza el paquete DomPDF para compilar el diseño HTML de la vista blade 'pdfs.reservation_ticket'
             * a un flujo de datos binario (PDF en memoria). Esto se realiza al vuelo sin guardar archivos en el disco,
             * optimizando el rendimiento y almacenamiento del servidor.
             */
            $pdfContent = null;
            try {
                // Carga la vista blade con la variable de la reservación e interpreta las directivas de estilos CSS
                $pdf = Pdf::loadView('pdfs.reservation_ticket', compact('reservation'));
                // Obtiene la representación en binario del PDF generado
                $pdfContent = $pdf->output();
            } catch (\Exception $e) {
                // Registro del error en logs para diagnóstico rápido sin detener el flujo principal
                Log::error('[ReservationController] Error al generar boleto PDF: ' . $e->getMessage());
            }

            /**
             * PASO 4: Envío del Correo de Confirmación vía SMTP (Mailtrap)
             * -------------------------------------------------------------
             * Si el PDF se generó de forma exitosa y el cliente ingresó un correo válido,
             * se despacha la clase mailable 'ReservationMail' pasando el comprobante PDF para ser
             * adjuntado de manera automática. Mailtrap captura este envío simulando un servidor de producción real.
             */
            if ($pdfContent && $reservation->email) {
                try {
                    // Envía el correo electrónico utilizando el driver de Laravel Mailer
                    Mail::to($reservation->email)->send(new ReservationMail($reservation, $pdfContent));
                    Log::info("[ReservationController] Correo de confirmación enviado a {$reservation->email} para la reserva #{$reservation->id}.");
                } catch (\Exception $e) {
                    Log::error('[ReservationController] Error al enviar correo de reservación: ' . $e->getMessage());
                }
            }

            /**
             * PASO 5: Notificación Reactiva por WhatsApp (UltraMsg API)
             * ----------------------------------------------------------
             * Se redacta un mensaje formateado utilizando sintaxis Markdown nativa de WhatsApp (asteriscos para negritas).
             * Incluye emojis dinámicos e información de la reserva.
             * A través del servicio inyectado 'WhatsAppService', se envía mediante una petición HTTP POST
             * a la API del proveedor externo.
             */
            if ($reservation->phone) {
                try {
                    // Obtención de los valores o textos por defecto si las relaciones están vacías
                    $movieTitle = $reservation->movie->title ?? 'una película';
                    $day        = $reservation->schedule->day ?? 'N/D';
                    $time       = $reservation->schedule->time ?? 'N/D';
                    $room       = $reservation->schedule->room ?? 'N/D';
                    $format     = $reservation->schedule->format ?? 'N/D';
                    
                    // Formateo del cuerpo del mensaje con soporte de negritas en WhatsApp
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

                    // Instancia el servicio cliente de UltraMsg y despacha la llamada a la API
                    $whatsapp = new WhatsAppService();
                    $whatsapp->sendMessage($reservation->phone, $message);
                } catch (\Exception $e) {
                    Log::error('[ReservationController] Error al enviar WhatsApp de reservación: ' . $e->getMessage());
                }
            }

            /**
             * RETORNO DE LA RESPUESTA API (JSON)
             * -----------------------------------
             * Retorna una respuesta JSON estructurada con código de estado HTTP 201 (Created).
             * Esto es interpretado por el frontend en 'main.js' para cerrar el modal y mostrar la tarjeta de éxito.
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
            // Manejador de fallos generales para evitar el colapso del sistema y mostrar un error controlado (HTTP 500)
            Log::error('[ReservationController] Error general al procesar reservación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar la reservación. Por favor intenta de nuevo.'
            ], 500);
        }
    }
}
