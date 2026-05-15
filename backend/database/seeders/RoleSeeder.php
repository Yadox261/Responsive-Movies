<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create([
            'name' => 'Admin',
            'description' => 'Acceso total al sistema'
        ]);

        \App\Models\Role::create([
            'name' => 'Editor',
            'description' => 'Puede gestionar películas pero no usuarios'
        ]);

        \App\Models\Role::create([
            'name' => 'Cliente',
            'description' => 'Usuario final que consulta el catálogo'
        ]);
    }
}
