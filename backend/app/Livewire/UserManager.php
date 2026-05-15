<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\User;
use App\Models\Role;

class UserManager extends Component
{
    public $users, $name, $email, $password, $role_id, $country_code, $phone_number, $user_id;
    public $isOpen = false;

    protected $listeners = ['deleteUser' => 'delete'];

    public function render()
    {
        $this->users = User::with('role')->get();
        $roles = Role::all();
        return view('livewire.user-manager', compact('roles'))->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = '';
        $this->country_code = '+52';
        $this->phone_number = '';
        $this->user_id = '';
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'role_id' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required|numeric',
        ];

        if (!$this->user_id) {
            $rules['password'] = 'required|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'country_code' => $this->country_code,
            'phone_number' => $this->phone_number,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        $this->dispatch('swal:modal', [
            'title' => $this->user_id ? '¡Actualizado!' : '¡Creado!',
            'text' => 'Usuario guardado con éxito.',
            'icon' => 'success'
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->role_id;
        $this->country_code = $user->country_code ?? '+52';
        $this->phone_number = $user->phone_number;
        $this->password = ''; // No cargar el password por seguridad

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => '¿Estás seguro?',
            'text' => '¡No podrás revertir esta acción!',
            'icon' => 'warning',
            'method' => 'deleteUser',
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        User::find($id)->delete();
        $this->dispatch('swal:modal', [
            'title' => '¡Eliminado!',
            'text' => 'El usuario ha sido eliminado con éxito.',
            'icon' => 'success'
        ]);
    }
}
