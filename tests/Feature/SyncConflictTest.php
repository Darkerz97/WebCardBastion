<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SyncConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_sales_marks_conflict_when_sale_number_exists_with_different_uuid(): void
    {
        $user = $this->makeUserWithRole(User::ROLE_ADMIN);

        Sale::query()->create([
            'uuid' => (string) Str::uuid(),
            'sale_number' => 'SALE-CONFLICT-0001',
            'order_channel' => Sale::CHANNEL_POS,
            'subtotal' => 10,
            'discount' => 0,
            'total' => 10,
            'status' => Sale::STATUS_COMPLETED,
            'payment_status' => Sale::PAYMENT_STATUS_PAID,
            'sold_at' => now(),
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/sync/upload-sales', [
                'device_code' => 'DEVICE-NOT-FOUND',
                'sales' => [
                    [
                        'uuid' => (string) Str::uuid(),
                        'sale_number' => 'SALE-CONFLICT-0001',
                        'status' => Sale::STATUS_COMPLETED,
                        'items' => [
                            [
                                'product_id' => 9999,
                                'quantity' => 1,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.0.status', 'conflict');
    }

    public function test_sync_catalog_includes_soft_deleted_categories(): void
    {
        $user = $this->makeUserWithRole(User::ROLE_ADMIN);

        $category = Category::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Categoria eliminable',
            'slug' => 'categoria-eliminable',
            'active' => true,
        ]);

        $category->delete();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/sync/catalog?include[]=categories')
            ->assertOk();

        $this->assertSame(1, collect($response->json('data.categories'))->where('uuid', $category->uuid)->count());
        $this->assertNotNull(collect($response->json('data.categories'))->firstWhere('uuid', $category->uuid)['deleted_at']);
    }

    private function makeUserWithRole(string $roleCode): User
    {
        $role = Role::query()->create([
            'name' => ucfirst($roleCode),
            'code' => $roleCode,
        ]);

        return User::factory()->create([
            'uuid' => (string) Str::uuid(),
            'role_id' => $role->id,
            'active' => true,
        ]);
    }
}
