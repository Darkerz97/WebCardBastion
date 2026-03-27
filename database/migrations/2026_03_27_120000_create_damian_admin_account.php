<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $adminRoleId = DB::table('roles')
            ->where('code', User::ROLE_ADMIN)
            ->value('id');

        if (! $adminRoleId) {
            return;
        }

        $existingUser = DB::table('users')
            ->where('email', 'damian97santacruz@gmail.com')
            ->first();

        DB::table('users')->updateOrInsert(
            ['email' => 'damian97santacruz@gmail.com'],
            [
                'uuid' => $existingUser->uuid ?? (string) Str::uuid(),
                'name' => 'jorge damian tenorio santacruz',
                'phone' => $existingUser->phone ?? null,
                'email_verified_at' => now(),
                'password' => Hash::make('2802damiaN'),
                'role_id' => $adminRoleId,
                'active' => true,
                'last_login_at' => $existingUser->last_login_at ?? null,
                'remember_token' => $existingUser->remember_token ?? Str::random(10),
                'deleted_at' => null,
                'created_at' => $existingUser->created_at ?? now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'damian97santacruz@gmail.com')
            ->delete();
    }
};
