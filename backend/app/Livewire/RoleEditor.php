<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Role;

class RoleEditor extends Component
{
    public $name, $description, $role_id;
    public $isEdit = false;

    public function mount(Role $role = null)
    {
        if ($role && $role->exists) {
            $this->isEdit = true;
            $this->role_id = $role->id;
            $this->name = $role->name;
            $this->description = $role->description;
        }
    }

    public function render()
    {
        return view('livewire.role-editor')->layout('layouts.app');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|unique:roles,name,' . $this->role_id,
            'description' => 'nullable',
        ]);

        Role::updateOrCreate(['id' => $this->role_id], [
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('swal', [
            'title' => $this->isEdit ? '¡Actualizado!' : '¡Creado!',
            'text' => 'Rol guardado con éxito.',
            'icon' => 'success'
        ]);

        return redirect()->route('roles.index');
    }
}
