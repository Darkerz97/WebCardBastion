<?php

namespace Tests\Feature;

use App\Models\CashClosure;
use App\Models\InventoryMovement;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesSyncFixtures;
use Tests\TestCase;

class SyncUploadEndpointsTest extends TestCase
{
    use CreatesSyncFixtures;
    use RefreshDatabase;

    public function test_upload_sales_creates_sale_and_supports_idempotency(): void
    {
        $user = $this->actingAsApiUser();
        $product = $this->createProduct(['stock' => 12]);
        $device = $this->createDevice(['device_code' => 'POS-SALES-01']);

        $payload = [
            'device_code' => $device->device_code,
            'sales' => [[
                'uuid' => (string) Str::uuid(),
                'user_uuid' => $user->uuid,
                'status' => Sale::STATUS_COMPLETED,
                'client_generated_at' => now()->subMinute()->toIso8601String(),
                'items' => [[
                    'product_uuid' => $product->uuid,
                    'quantity' => 2,
                    'unit_price' => 20,
                ]],
            ]],
        ];

        $this->postJson('/api/sync/upload-sales', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'created');

        $this->postJson('/api/sync/upload-sales', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'skipped');
    }

    public function test_upload_sales_returns_conflict_for_missing_product_reference(): void
    {
        $user = $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-SALES-02']);

        $this->postJson('/api/sync/upload-sales', [
            'device_code' => $device->device_code,
            'sales' => [[
                'uuid' => (string) Str::uuid(),
                'user_uuid' => $user->uuid,
                'status' => Sale::STATUS_COMPLETED,
                'items' => [[
                    'product_uuid' => (string) Str::uuid(),
                    'quantity' => 1,
                ]],
            ]],
        ])
            ->assertOk()
            ->assertJsonPath('data.0.status', 'conflict')
            ->assertJsonPath('data.0.code', 'missing_product');
    }

    public function test_upload_sales_returns_validation_error_for_invalid_payload(): void
    {
        $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-SALES-03']);

        $this->postJson('/api/sync/upload-sales', [
            'device_code' => $device->device_code,
            'sales' => [[
                'uuid' => (string) Str::uuid(),
                'status' => Sale::STATUS_COMPLETED,
                'items' => [],
            ]],
        ])->assertStatus(422);
    }

    public function test_upload_cash_closures_creates_and_skips_duplicate_uuid(): void
    {
        $user = $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-CLOSURE-01']);
        $closureUuid = (string) Str::uuid();

        $payload = [
            'device_code' => $device->device_code,
            'closures' => [[
                'uuid' => $closureUuid,
                'user_uuid' => $user->uuid,
                'opening_amount' => 100,
                'cash_sales' => 50,
                'card_sales' => 25,
                'closing_amount' => 150,
                'status' => CashClosure::STATUS_CLOSED,
                'source' => CashClosure::SOURCE_POS,
            ]],
        ];

        $this->postJson('/api/sync/upload-cash-closures', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'created');

        $this->postJson('/api/sync/upload-cash-closures', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'skipped');
    }

    public function test_upload_cash_closures_returns_conflict_for_missing_user(): void
    {
        $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-CLOSURE-02']);

        $this->postJson('/api/sync/upload-cash-closures', [
            'device_code' => $device->device_code,
            'closures' => [[
                'uuid' => (string) Str::uuid(),
                'user_uuid' => (string) Str::uuid(),
                'closing_amount' => 90,
                'status' => CashClosure::STATUS_CLOSED,
                'source' => CashClosure::SOURCE_POS,
            ]],
        ])
            ->assertOk()
            ->assertJsonPath('data.0.status', 'conflict')
            ->assertJsonPath('data.0.code', 'missing_user');
    }

    public function test_upload_cash_closures_returns_validation_error(): void
    {
        $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-CLOSURE-03']);

        $this->postJson('/api/sync/upload-cash-closures', [
            'device_code' => $device->device_code,
            'closures' => [[
                'uuid' => (string) Str::uuid(),
                'source' => CashClosure::SOURCE_POS,
            ]],
        ])->assertStatus(422);
    }

    public function test_upload_inventory_movements_creates_and_skips_duplicate_uuid(): void
    {
        $this->actingAsApiUser();
        $product = $this->createProduct(['stock' => 4]);
        $device = $this->createDevice(['device_code' => 'POS-INV-01']);
        $movementUuid = (string) Str::uuid();

        $payload = [
            'device_code' => $device->device_code,
            'movements' => [[
                'uuid' => $movementUuid,
                'product_uuid' => $product->uuid,
                'movement_type' => InventoryMovement::TYPE_RESTOCK,
                'direction' => InventoryMovement::DIRECTION_IN,
                'quantity' => 3,
                'source' => InventoryMovement::SOURCE_POS,
            ]],
        ];

        $this->postJson('/api/sync/upload-inventory-movements', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'created');

        $this->postJson('/api/sync/upload-inventory-movements', $payload)
            ->assertOk()
            ->assertJsonPath('data.0.status', 'skipped');
    }

    public function test_upload_inventory_movements_returns_conflict_for_missing_product(): void
    {
        $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-INV-02']);

        $this->postJson('/api/sync/upload-inventory-movements', [
            'device_code' => $device->device_code,
            'movements' => [[
                'uuid' => (string) Str::uuid(),
                'product_uuid' => (string) Str::uuid(),
                'movement_type' => InventoryMovement::TYPE_RESTOCK,
                'direction' => InventoryMovement::DIRECTION_IN,
                'quantity' => 1,
                'source' => InventoryMovement::SOURCE_POS,
            ]],
        ])
            ->assertOk()
            ->assertJsonPath('data.0.status', 'conflict')
            ->assertJsonPath('data.0.code', 'missing_product');
    }

    public function test_upload_inventory_movements_returns_validation_error(): void
    {
        $this->actingAsApiUser();
        $device = $this->createDevice(['device_code' => 'POS-INV-03']);

        $this->postJson('/api/sync/upload-inventory-movements', [
            'device_code' => $device->device_code,
            'movements' => [[
                'uuid' => (string) Str::uuid(),
                'movement_type' => InventoryMovement::TYPE_RESTOCK,
                'direction' => InventoryMovement::DIRECTION_IN,
                'source' => InventoryMovement::SOURCE_POS,
            ]],
        ])->assertStatus(422);
    }
}
