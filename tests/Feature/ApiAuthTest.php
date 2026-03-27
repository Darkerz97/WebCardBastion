<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesSyncFixtures;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use CreatesSyncFixtures;
    use RefreshDatabase;

    public function test_api_login_returns_token_for_backoffice_user(): void
    {
        $user = $this->createBackofficeUser(User::ROLE_ADMIN, [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'POS Local',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_api_login_rejects_player_role(): void
    {
        $user = $this->createBackofficeUser(User::ROLE_PLAYER, [
            'email' => 'player@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertForbidden();
    }
}
