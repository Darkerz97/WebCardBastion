<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

trait CreatesSyncFixtures
{
    protected function createRole(string $code): Role
    {
        return Role::query()->firstOrCreate(
            ['code' => $code],
            ['name' => ucfirst($code)],
        );
    }

    protected function createBackofficeUser(string $roleCode = User::ROLE_ADMIN, array $attributes = []): User
    {
        $role = $this->createRole($roleCode);

        return User::factory()->create([
            'uuid' => (string) Str::uuid(),
            'role_id' => $role->id,
            'active' => true,
            ...$attributes,
        ]);
    }

    protected function actingAsApiUser(?User $user = null, array $abilities = ['*']): User
    {
        $user ??= $this->createBackofficeUser();
        Sanctum::actingAs($user->loadMissing('role'), $abilities);

        return $user;
    }

    protected function createDevice(array $attributes = []): Device
    {
        return Device::query()->create([
            'uuid' => (string) Str::uuid(),
            'device_code' => 'POS-'.Str::upper(Str::random(8)),
            'name' => 'POS Test',
            'type' => Device::TYPE_POS,
            'active' => true,
            ...$attributes,
        ]);
    }

    protected function createCategory(array $attributes = []): Category
    {
        return Category::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Categoria '.Str::upper(Str::random(4)),
            'slug' => 'categoria-'.Str::lower(Str::random(6)),
            'active' => true,
            ...$attributes,
        ]);
    }

    protected function createProduct(array $attributes = []): Product
    {
        $category = $attributes['category_id'] ?? $this->createCategory()->id;

        return Product::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Producto '.Str::upper(Str::random(4)),
            'slug' => 'producto-'.Str::lower(Str::random(6)),
            'sku' => 'SKU-'.Str::upper(Str::random(6)),
            'barcode' => '750'.random_int(1000000000, 9999999999),
            'category_id' => $category,
            'cost' => 10,
            'price' => 20,
            'stock' => 10,
            'active' => true,
            'publish_to_store' => false,
            ...$attributes,
        ]);
    }

    protected function createCustomer(array $attributes = []): Customer
    {
        return Customer::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Cliente '.Str::upper(Str::random(4)),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '555'.random_int(1000000, 9999999),
            'active' => true,
            'credit_balance' => 0,
            ...$attributes,
        ]);
    }
}
