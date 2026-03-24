<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('code', User::ROLE_ADMIN)->firstOrFail();
        $managerRole = Role::query()->where('code', User::ROLE_MANAGER)->firstOrFail();
        $cashierRole = Role::query()->where('code', User::ROLE_CASHIER)->firstOrFail();
        $playerRole = Role::query()->where('code', User::ROLE_PLAYER)->firstOrFail();

        $users = [
            [
                'email' => env('ADMIN_EMAIL', 'admin@cardbastion.test'),
                'name' => env('ADMIN_NAME', 'Card Bastion Admin'),
                'phone' => env('ADMIN_PHONE', '5550000000'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role_id' => $adminRole->id,
            ],
            [
                'email' => 'manager@cardbastion.test',
                'name' => 'Card Bastion Manager',
                'phone' => '5550000001',
                'password' => 'password',
                'role_id' => $managerRole->id,
            ],
            [
                'email' => 'cashier@cardbastion.test',
                'name' => 'Card Bastion Cashier',
                'phone' => '5550000002',
                'password' => 'password',
                'role_id' => $cashierRole->id,
            ],
            [
                'email' => 'player@cardbastion.test',
                'name' => 'Demo Player',
                'phone' => '5550000003',
                'password' => 'password',
                'role_id' => $playerRole->id,
            ],
        ];

        foreach ($users as $user) {
            $createdUser = User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'uuid' => (string) Str::uuid(),
                    'active' => true,
                    'email_verified_at' => now(),
                ],
            );

            if ($createdUser->hasRole(User::ROLE_PLAYER)) {
                Customer::query()->updateOrCreate(
                    ['user_id' => $createdUser->id],
                    [
                        'uuid' => (string) Str::uuid(),
                        'name' => $createdUser->name,
                        'phone' => $createdUser->phone,
                        'email' => $createdUser->email,
                        'notes' => 'Perfil de jugador demo.',
                        'credit_balance' => 0,
                        'active' => true,
                    ],
                );
            }
        }
    }
}
