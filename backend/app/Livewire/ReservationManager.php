<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use Livewire\WithPagination;
use App\Mail\ReservationMail;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Clase ReservationManager (Componente Livewire)
 * -----------------------------------------------
 * Este componente gestiona el panel administrativo de reservaciones en tiempo real (SPA).
 * Provee filtros avanzados de búsqueda y paginación reactiva, además de integrarse
 * con SweetAlert2 para el flujo seguro de cancelación/borrado de registros.
 * Ahora también permite la creación manual de reservaciones desde la administración.
 */
class ReservationManager extends Component
{
    // Habilita la paginación reactiva integrada de Livewire (evita recargas completas del navegador)
    use WithPagination;

    /**
     * Variable reactiva enlazada al input del buscador en la vista ('wire:model.live').
     * Cada vez que el usuario escribe, Livewire detecta el cambio y re-renderiza la tabla automáticamente.
     */
    public $search = '';

    /**
     * Propiedades reactivas para el modal de creación de reservación.
     */
    public $showCreateModal = false;
    public $movie_id = '';
    public $schedule_id = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $seats = 1;

    // Colecciones temporales para combos en el formulario
    public $moviesList = [];
    public $schedulesList = [];

    /**
     * Eventos de Livewire escuchados por este componente.
     * Vincula el disparo asíncrono 'deleteReservation' enviado desde el cliente (SweetAlert2)
     * con el método local 'delete' del backend para remover registros.
     */
    protected $listeners = ['deleteReservation' => 'delete'];

    /**
     * Ciclo de Vida: Reinicio de Página
     * ---------------------------------
     * 'updatingSearch()' se ejecuta justo antes de cambiar la variable '$search'.
     * Reinicia el puntero de la paginación a la primera hoja para evitar que una búsqueda
     * con pocos resultados quede oculta si el usuario estaba en la página 3 o superior.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Carga dinámicamente los horarios de la película seleccionada.
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
     * Abre el modal de creación y carga las películas disponibles.
     */
    public function openCreateModal()
    {
        $this->reset(['movie_id', 'schedule_id', 'name', 'email', 'phone', 'seats', 'schedulesList']);
        $this->moviesList = \App\Models\Movie::orderBy('title')->get();
        $this->seats = 1;
        $this->showCreateModal = true;
    }

    /**
     * Cierra el modal de creación.
     */
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    /**
     * Guarda la reservación creada por el administrador, genera el PDF del boleto,
     * envía el correo SMTP y dispara la notificación de WhatsApp.
     */
    public function saveReservation()
    {
        $validated = $this->validate([
            'movie_id'    => 'required|exists:movies,id',
            'schedule_id' => 'required|exists:schedules,id',
            'name'        => 'required|string|min:3|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|min:7|max:20',
            'seats'       => 'required|integer|min:1|max:10',
        ]);

        try {
            // Asegura el prefijo internacional de México para WhatsApp
            if (!str_starts_with($validated['phone'], '+')) {
                $validated['phone'] = '+52' . ltrim($validated['phone'], '0');
            }

            // Crear la reservación
            $reservation = Reservation::create($validated);
            
            // Cargar relaciones
            $reservation->load(['movie', 'schedule']);

            // Generar el PDF del Boleto en memoria
            $pdfContent = null;
            try {
                $pdf = Pdf::loadView('pdfs.reservation_ticket', compact('reservation'));
                $pdfContent = $pdf->output();
            } catch (\Exception $e) {
                Log::error('[ReservationManager] Error al generar boleto PDF: ' . $e->getMessage());
            }

            // Enviar Correo de Confirmación con PDF adjunto vía SMTP
            if ($pdfContent && $reservation->email) {
                try {
                    Mail::to($reservation->email)->send(new ReservationMail($reservation, $pdfContent));
                    Log::info("[ReservationManager] Correo enviado a {$reservation->email} para la reserva #{$reservation->id}.");
                } catch (\Exception $e) {
                    Log::error('[ReservationManager] Error al enviar correo de reservación: ' . $e->getMessage());
                }
            }

            // Enviar mensaje de WhatsApp mediante UltraMsg
            if ($reservation->phone) {
                try {
                    $movieTitle = $reservation->movie->title ?? 'una película';
                    $day = $reservation->schedule->day ?? 'N/D';
                    $time = $reservation->schedule->time ?? 'N/D';
                    $room = $reservation->schedule->room ?? 'N/D';
                    $format = $reservation->schedule->format ?? 'N/D';

                    $message = "🎟️ *Word of the Movies - Reservación Confirmada (Administración)*\n\n"
                        . "¡Hola *{$reservation->name}*! Tu reservación para *{$movieTitle}* ha sido creada exitosamente por el administrador:\n\n"
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
                    Log::error('[ReservationManager] Error al enviar WhatsApp de reservación: ' . $e->getMessage());
                }
            }

            // Cerrar el modal y refrescar la tabla
            $this->showCreateModal = false;

            // Enviar alerta SweetAlert2 de éxito al cliente
            $this->dispatch('swal:modal', [
                'title' => '¡Reservación Creada!',
                'text' => 'La reservación se ha registrado y notificado exitosamente.',
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('[ReservationManager] Error general al guardar reservación: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar la reservación.');
        }
    }

    /**
     * Método Renderizador Principal
     * ------------------------------
     * Recolecta la lista de reservaciones aplicando un filtro de coincidencia difusa (LIKE) en
     * varias tablas relacionales y despacha la vista del administrador.
     */
    public function render()
    {
        // Consulta relacional optimizada (eager-loading) para evitar el problema de consultas N+1
        $reservations = Reservation::with(['movie', 'schedule'])
            ->where(function ($query) {
                // Filtro dinámico multidominio sobre datos del cliente o título de la película
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhereHas('movie', function ($q) {
                          $q->where('title', 'like', '%' . $this->search . '%');
                      });
            })
            // Mantiene el orden cronológico descendente (las más recientes primero)
            ->orderBy('created_at', 'desc')
            // Paginación eficiente directamente desde MySQL a nivel servidor (10 registros por página)
            ->paginate(10);

        // Renderiza el componente dentro de la plantilla base 'layouts.app'
        return view('livewire.reservation-manager', compact('reservations'))->layout('layouts.app');
    }

    /**
     * Dispara la Alerta de Confirmación
     * ----------------------------------
     * Envía un evento al frontend con parámetros de configuración para SweetAlert2.
     * El frontend se encarga de mostrar la confirmación y, si es aceptada, devuelve
     * la llamada asíncrona a 'deleteReservation' (vinculado a 'delete').
     * 
     * @param int $id ID de la reservación a cancelar.
     */
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => '¿Cancelar Reservación?',
            'text' => 'Esta acción no se puede deshacer y el boleto del cliente dejará de ser válido.',
            'icon' => 'warning',
            'method' => 'deleteReservation',
            'id' => $id
        ]);
    }

    /**
     * Eliminación de Reservación
     * ---------------------------
     * Método que destruye de manera lógica o física el registro en la base de datos.
     * 
     * @param int $id ID del registro en MySQL.
     */
    public function delete($id)
    {
        $reservation = Reservation::find($id);
        if ($reservation) {
            // Borra la fila correspondiente de la tabla 'reservations'
            $reservation->delete();
            
            // Notifica al frontend con SweetAlert2 para reportar éxito de la transacción
            $this->dispatch('swal:modal', [
                'title' => '¡Cancelada!',
                'text' => 'La reservación ha sido cancelada y eliminada con éxito.',
                'icon' => 'success'
            ]);
        }
    }
}
