<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'code' => User::ROLE_ADMIN,
                'description' => 'Acceso total al sistema y configuración general.',
            ],
            [
                'name' => 'Gerente',
                'code' => User::ROLE_MANAGER,
                'description' => 'Gestión operativa, reportes y supervisión.',
            ],
            [
                'name' => 'Cajero',
                'code' => User::ROLE_CASHIER,
                'description' => 'Registro de ventas y consulta operativa.',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['code' => $role['code']], $role);
        }
    }
}
