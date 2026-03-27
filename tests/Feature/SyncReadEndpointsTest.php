<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesSyncFixtures;
use Tests\TestCase;

class SyncReadEndpointsTest extends TestCase
{
    use CreatesSyncFixtures;
    use RefreshDatabase;

    public function test_sync_products_returns_filtered_records_by_updated_since(): void
    {
        $this->actingAsApiUser();

        $oldProduct = $this->createProduct();
        $recentProduct = $this->createProduct();

        $oldProduct->forceFill(['updated_at' => now()->subDays(3)])->saveQuietly();
        $recentProduct->forceFill(['updated_at' => now()->subMinutes(30)])->saveQuietly();

        $this->getJson('/api/sync/products?updated_since='.urlencode(now()->subDay()->toIso8601String()))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonMissing(['uuid' => $oldProduct->uuid])
            ->assertJsonFragment(['uuid' => $recentProduct->uuid]);
    }

    public function test_sync_products_returns_validation_error_for_invalid_updated_since(): void
    {
        $this->actingAsApiUser();

        $this->getJson('/api/sync/products?updated_since=nope')
            ->assertStatus(422);
    }

    public function test_sync_customers_returns_filtered_records_by_updated_since(): void
    {
        $this->actingAsApiUser();

        $oldCustomer = $this->createCustomer();
        $recentCustomer = $this->createCustomer();

        $oldCustomer->forceFill(['updated_at' => now()->subDays(3)])->saveQuietly();
        $recentCustomer->forceFill(['updated_at' => now()->subMinutes(30)])->saveQuietly();

        $this->getJson('/api/sync/customers?updated_since='.urlencode(now()->subDay()->toIso8601String()))
            ->assertOk()
            ->assertJsonMissing(['uuid' => $oldCustomer->uuid])
            ->assertJsonFragment(['uuid' => $recentCustomer->uuid]);
    }

    public function test_sync_catalog_returns_requested_entities_and_meta_counts(): void
    {
        $this->actingAsApiUser();

        $product = $this->createProduct();
        $customer = $this->createCustomer();
        $category = $product->categoryModel()->first();

        $this->getJson('/api/sync/catalog?include[]=products&include[]=categories&include[]=customers')
            ->assertOk()
            ->assertJsonPath('meta.counts.products', 1)
            ->assertJsonPath('meta.counts.categories', 1)
            ->assertJsonPath('meta.counts.customers', 1)
            ->assertJsonFragment(['uuid' => $product->uuid])
            ->assertJsonFragment(['uuid' => $customer->uuid])
            ->assertJsonFragment(['uuid' => $category->uuid]);
    }
}
