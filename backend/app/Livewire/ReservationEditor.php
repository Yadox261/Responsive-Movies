<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use App\Mail\ReservationMail;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Clase ReservationEditor (Componente Livewire)
 * -----------------------------------------------
 * Este componente representa la nueva vista (página independiente) encargada de
 * registrar (crear) o editar reservaciones cinematográficas de forma manual desde el panel.
 * Cuenta con un diseño adaptado y reactividad en tiempo real.
 */
class ReservationEditor extends Component
{
    // Propiedades del formulario
    public $movie_id = '';
    public $schedule_id = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $seats = 1;

    // Propiedades de control de estado
    public $reservation_id;
    public $isEdit = false;

    // Colecciones de soporte
    public $moviesList = [];
    public $schedulesList = [];

    /**
     * Inicialización del Componente
     * -----------------------------
     * Carga el registro existente si se proporciona un ID de reservación (modo edición),
     * o inicializa un formulario limpio con valores predeterminados (modo creación).
     *
     * @param Reservation|null $reservation Instancia inyectada por Route Model Binding.
     */
    public function mount(?Reservation $reservation = null)
    {
        // Carga la lista global de películas para el desplegable principal
        $this->moviesList = \App\Models\Movie::orderBy('title')->get();

        if ($reservation && $reservation->exists) {
            $this->isEdit = true;
            $this->reservation_id = $reservation->id;
            $this->movie_id = $reservation->movie_id;
            $this->schedule_id = $reservation->schedule_id;
            $this->name = $reservation->name;
            $this->email = $reservation->email;
            
            // Si el teléfono tiene el prefijo de México, lo removemos para mostrar solo los 10 dígitos locales
            $this->phone = str_starts_with($reservation->phone, '+52')
                ? substr($reservation->phone, 3)
                : $reservation->phone;

            $this->seats = $reservation->seats;

            // Cargar los horarios correspondientes de la película actual en edición
            $movie = \App\Models\Movie::with('schedules')->find($this->movie_id);
            $this->schedulesList = $movie ? $movie->schedules : [];
        } else {
            $this->isEdit = false;
            $this->seats = 1;
        }
    }

    /**
     * Carga dinámicamente los horarios de la película seleccionada.
     * Se dispara automáticamente cuando cambia '$movie_id' gracias a 'wire:model.live'.
     */
    public function updatedMovieId($value)
    {
        if ($value) {
            $movie = \App\Models\Movie::with('schedules')->find($value);
            $this->schedulesList = $movie ? $movie->schedules : [];
            $this->schedule_id = '';
        } else {
            $this->schedulesList = [];
            $this->schedule_id = '';
        }
    }

    /**
     * Renderizador del Componente
     * Retorna la vista correspondiente dentro de la plantilla maestra de Jetstream.
     */
    public function render()
    {
        return view('livewire.reservation-editor')->layout('layouts.app');
    }

    /**
     * Guarda la reservación en base de datos.
     * Crea un nuevo registro o actualiza el existente, genera el boleto PDF y envía
     * notificaciones automatizadas por Correo SMTP y WhatsApp (UltraMsg).
     */
    public function save()
    {
        // Validar los campos del formulario
        $validated = $this->validate([
            'movie_id'    => 'required|exists:movies,id',
            'schedule_id' => 'required|exists:schedules,id',
            'name'        => 'required|string|min:3|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|min:7|max:20',
            'seats'       => 'required|integer|min:1|max:10',
        ], [
            'movie_id.required'    => 'La película es obligatoria.',
            'schedule_id.required' => 'El horario/función es obligatorio.',
            'name.required'        => 'El nombre completo del cliente es obligatorio.',
            'email.required'       => 'El correo electrónico es obligatorio.',
            'email.email'          => 'Ingresa un correo electrónico con formato válido.',
            'phone.required'       => 'El teléfono de celular/WhatsApp es obligatorio.',
            'seats.required'       => 'La cantidad de asientos es obligatoria.',
        ]);

        try {
            // Asegurar prefijo internacional de México (+52) para WhatsApp
            if (!str_starts_with($validated['phone'], '+')) {
                $validated['phone'] = '+52' . ltrim($validated['phone'], '0');
            }

            // Crear o actualizar la reservación
            $reservation = Reservation::updateOrCreate(
                ['id' => $this->reservation_id],
                $validated
            );

            // Cargar relaciones necesarias para boletos y correos
            $reservation->load(['movie', 'schedule']);

            // Generar el PDF del Boleto en memoria
            $pdfContent = null;
            try {
                $pdf = Pdf::loadView('pdfs.reservation_ticket', compact('reservation'));
                $pdfContent = $pdf->output();
            } catch (\Exception $e) {
                Log::error('[ReservationEditor] Error al generar boleto PDF: ' . $e->getMessage());
            }

            // Enviar Correo de Confirmación SMTP con PDF adjunto
            if ($pdfContent && $reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new ReservationMail($reservation, $pdfContent));
                    Log::info("[ReservationEditor] Correo enviado a {$reservation->email} para la reserva #{$reservation->id}.");
                } catch (\Exception $e) {
                    Log::error('[ReservationEditor] Error al enviar correo de reservación: ' . $e->getMessage());
                }
            }

            // Enviar mensaje a WhatsApp vía UltraMsg
            if ($reservation->phone) {
                try {
                    $movieTitle = $reservation->movie->title ?? 'una película';
                    $day = $reservation->schedule->day ?? 'N/D';
                    $time = $reservation->schedule->time ?? 'N/D';
                    $room = $reservation->schedule->room ?? 'N/D';
                    $format = $reservation->schedule->format ?? 'N/D';

                    $actionText = $this->isEdit ? 'actualizada' : 'creada';
                    $message = "🎟️ *Word of the Movies - Reservación Confirmada (Administración)*\n\n"
                        . "¡Hola *{$reservation->name}*! Tu reservación para *{$movieTitle}* ha sido *{$actionText}* exitosamente por el administrador:\n\n"
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
                    Log::error('[ReservationEditor] Error al enviar WhatsApp de reservación: ' . $e->getMessage());
                }
            }

            // Disparar flash message SweetAlert2 para la vista de origen redireccionada
            session()->flash('swal', [
                'title' => $this->isEdit ? '¡Actualizado!' : '¡Creado!',
                'text'  => $this->isEdit ? 'La reservación ha sido modificada con éxito.' : 'La reservación se ha registrado y notificado con éxito.',
                'icon'  => 'success'
            ]);

            // Redireccionar al listado principal de reservaciones
            return redirect()->route('reservations.index');

        } catch (\Exception $e) {
            Log::error('[ReservationEditor] Error al guardar reservación: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar la reservación.');
        }
    }
}
