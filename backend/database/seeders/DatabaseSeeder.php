<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Sembrar los roles primero (Admin, Editor, Cliente)
        $this->call(RoleSeeder::class);

        // 2. Sembrar películas de ejemplo
        $this->call(MovieSeeder::class);

        // 3. Crear usuario Administrador
        User::create([
            'name'         => 'Administrador',
            'email'        => 'admin@movies.com',
            'password'     => Hash::make('123456789'),
            'role_id'      => 1,
            'country_code' => '+52',
            'phone_number' => '5500000001',
        ]);

        // 4. Crear Editores (recibirán el reporte matutino)
        User::create([
            'name'         => 'Editor Principal',
            'email'        => 'editor@movies.com',
            'password'     => Hash::make('123456789'),
            'role_id'      => 2,
            'country_code' => '+52',
            'phone_number' => '5512345678',
        ]);

        User::create([
            'name'         => 'Editor Secundario',
            'email'        => 'editor2@movies.com',
            'password'     => Hash::make('123456789'),
            'role_id'      => 2,
            'country_code' => '+52',
            'phone_number' => '5598765432',
        ]);

        // 5. Crear un usuario Cliente de prueba
        User::create([
            'name'         => 'Cliente Demo',
            'email'        => 'cliente@movies.com',
            'password'     => Hash::make('123456789'),
            'role_id'      => 3,
            'country_code' => '+52',
            'phone_number' => '5511223344',
        ]);
    }
}
