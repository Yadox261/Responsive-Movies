<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Role;

class RoleManager extends Component
{
    protected $listeners = ['deleteRole' => 'delete'];

    public function render()
    {
        $roles = Role::all();
        return view('livewire.role-manager', compact('roles'))->layout('layouts.app');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => '¿Eliminar Rol?',
            'text' => 'Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'method' => 'deleteRole',
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        Role::find($id)->delete();
        $this->dispatch('swal:modal', [
            'title' => '¡Eliminado!',
            'text' => 'El rol ha sido eliminado con éxito.',
            'icon' => 'success'
        ]);
    }
}
