<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InventoryMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_sale_creates_inventory_movements_and_updates_stock(): void
    {
        $user = $this->makeUserWithRole(User::ROLE_ADMIN);
        $product = Product::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Producto test',
            'slug' => 'producto-test',
            'sku' => 'SKU-TEST-1',
            'barcode' => '7501111111111',
            'cost' => 50,
            'price' => 100,
            'stock' => 10,
            'active' => true,
            'publish_to_store' => false,
        ]);

        $device = Device::query()->create([
            'uuid' => (string) Str::uuid(),
            'device_code' => 'POS-TEST-01',
            'name' => 'POS Test',
            'type' => Device::TYPE_POS,
            'active' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/sales', [
                'user_id' => $user->id,
                'device_id' => $device->id,
                'status' => 'completed',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                        'unit_price' => 100,
                    ],
                ],
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $product->refresh();

        $this->assertSame(8, $product->stock);
        $this->assertDatabaseCount('inventory_movements', 1);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => InventoryMovement::TYPE_SALE,
            'direction' => InventoryMovement::DIRECTION_OUT,
            'quantity' => 2,
            'stock_before' => 10,
            'stock_after' => 8,
            'source' => InventoryMovement::SOURCE_SERVER,
        ]);
    }

    public function test_manual_adjustment_endpoint_creates_audited_movement_and_updates_stock(): void
    {
        $user = $this->makeUserWithRole(User::ROLE_ADMIN);
        $product = Product::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Producto ajuste',
            'slug' => 'producto-ajuste',
            'sku' => 'SKU-TEST-2',
            'barcode' => '7502222222222',
            'cost' => 10,
            'price' => 20,
            'stock' => 4,
            'active' => true,
            'publish_to_store' => false,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/inventory-movements', [
                'product_id' => $product->id,
                'movement_type' => InventoryMovement::TYPE_MANUAL_ADJUSTMENT,
                'direction' => InventoryMovement::DIRECTION_IN,
                'quantity' => 3,
                'source' => InventoryMovement::SOURCE_SERVER,
                'notes' => 'Ajuste manual de prueba',
            ])
            ->assertCreated()
            ->assertJsonPath('data.stock_after', 7);

        $product->refresh();

        $this->assertSame(7, $product->stock);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => InventoryMovement::TYPE_MANUAL_ADJUSTMENT,
            'direction' => InventoryMovement::DIRECTION_IN,
            'stock_before' => 4,
            'stock_after' => 7,
        ]);
    }

    public function test_sync_upload_inventory_movements_is_idempotent_by_uuid(): void
    {
        $user = $this->makeUserWithRole(User::ROLE_ADMIN);
        $product = Product::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Producto sync',
            'slug' => 'producto-sync',
            'sku' => 'SKU-TEST-3',
            'barcode' => '7503333333333',
            'cost' => 15,
            'price' => 25,
            'stock' => 5,
            'active' => true,
            'publish_to_store' => false,
        ]);

        Device::query()->create([
            'uuid' => (string) Str::uuid(),
            'device_code' => 'POS-TEST-02',
            'name' => 'POS Sync',
            'type' => Device::TYPE_POS,
            'active' => true,
        ]);

        $movementUuid = (string) Str::uuid();
        $payload = [
            'device_code' => 'POS-TEST-02',
            'movements' => [
                [
                    'uuid' => $movementUuid,
                    'product_id' => $product->id,
                    'movement_type' => InventoryMovement::TYPE_RESTOCK,
                    'direction' => InventoryMovement::DIRECTION_IN,
                    'quantity' => 2,
                    'source' => InventoryMovement::SOURCE_POS,
                ],
            ],
        ];

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/sync/upload-inventory-movements', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'created');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/sync/upload-inventory-movements', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'skipped');

        $product->refresh();

        $this->assertSame(7, $product->stock);
        $this->assertDatabaseCount('inventory_movements', 1);
        $this->assertDatabaseHas('sync_logs', [
            'entity_type' => 'inventory_movement',
            'entity_uuid' => $movementUuid,
        ]);
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
