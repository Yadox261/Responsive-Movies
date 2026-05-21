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
