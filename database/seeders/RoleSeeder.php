<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'guest', 'description' => 'Usuario invitado con acceso limitado'],
            ['name' => 'client', 'description' => 'Cliente del sistema'],
            ['name' => 'seller', 'description' => 'Vendedor con permisos de ventas'],
            ['name' => 'supervisor', 'description' => 'Supervisor con permisos ampliados'],
            ['name' => 'admin', 'description' => 'Administrador con acceso total'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'description' => $role['description']
            ]);
        }
    }
}
