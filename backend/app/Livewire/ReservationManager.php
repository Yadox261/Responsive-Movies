<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use Livewire\WithPagination;

class ReservationManager extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['deleteReservation' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $reservations = Reservation::with(['movie', 'schedule'])
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhereHas('movie', function ($q) {
                          $q->where('title', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.reservation-manager', compact('reservations'))->layout('layouts.app');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => '¿Cancelar Reservación?',
            'text' => 'Esta acción no se puede deshacer y el boleto dejará de ser válido.',
            'icon' => 'warning',
            'method' => 'deleteReservation',
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        $reservation = Reservation::find($id);
        if ($reservation) {
            $reservation->delete();
            $this->dispatch('swal:modal', [
                'title' => '¡Cancelada!',
                'text' => 'La reservación ha sido cancelada y eliminada con éxito.',
                'icon' => 'success'
            ]);
        }
    }
}
