<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SyncConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_sales_marks_conflict_when_sale_number_exists_with_different_uuid(): void
    {
        $user = $this->makeUserWithRole(User::ROLE_ADMIN);
        $product = Product::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Producto conflicto',
            'slug' => 'producto-conflicto',
            'sku' => 'SKU-CONFLICT-1',
            'barcode' => '7504444444444',
            'cost' => 5,
            'price' => 10,
            'stock' => 10,
            'active' => true,
            'publish_to_store' => false,
        ]);

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

        Sanctum::actingAs($user->loadMissing('role'), ['*']);

        $this
            ->postJson('/api/sync/upload-sales', [
                'device_code' => 'DEVICE-NOT-FOUND',
                'sales' => [
                    [
                        'uuid' => (string) Str::uuid(),
                        'sale_number' => 'SALE-CONFLICT-0001',
                        'status' => Sale::STATUS_COMPLETED,
                        'items' => [
                            [
                                'product_id' => $product->id,
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

        Sanctum::actingAs($user->loadMissing('role'), ['*']);

        $response = $this
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
