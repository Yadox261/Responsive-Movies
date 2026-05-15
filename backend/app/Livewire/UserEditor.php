<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\User;
use App\Models\Role;

class UserEditor extends Component
{
    public $name, $email, $password, $role_id, $country_code, $phone_number, $user_id;
    public $isEdit = false;

    public function mount(User $user = null)
    {
        if ($user && $user->exists) {
            $this->isEdit = true;
            $this->user_id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_id = $user->role_id;
            $this->country_code = $user->country_code ?? '+52';
            $this->phone_number = $user->phone_number;
        } else {
            $this->country_code = '+52';
        }
    }

    public function render()
    {
        $roles = Role::all();
        return view('livewire.user-editor', compact('roles'))->layout('layouts.app');
    }

    public function save()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'role_id' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required|numeric',
        ];

        if (!$this->isEdit) {
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

        session()->flash('swal', [
            'title' => $this->isEdit ? '¡Actualizado!' : '¡Creado!',
            'text' => 'Usuario guardado con éxito.',
            'icon' => 'success'
        ]);

        return redirect()->route('users.index');
    }
}
