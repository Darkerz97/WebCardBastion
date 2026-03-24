<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('code', User::ROLE_ADMIN)->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@cardbastion.test')],
            [
                'uuid' => (string) Str::uuid(),
                'name' => env('ADMIN_NAME', 'Card Bastion Admin'),
                'phone' => env('ADMIN_PHONE', '5550000000'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role_id' => $adminRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
