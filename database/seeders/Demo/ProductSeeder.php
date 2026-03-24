<?php

namespace Database\Seeders\Demo;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Booster Set Alpha',
                'slug' => 'booster-set-alpha',
                'sku' => 'BST-ALPHA-001',
                'barcode' => '750100000001',
                'description' => 'Booster sellado para juego competitivo.',
                'short_description' => 'Expansion sellada para drafts y torneos semanales.',
                'category' => 'Boosters',
                'cost' => 60,
                'price' => 95,
                'stock' => 40,
                'active' => true,
                'featured' => true,
                'publish_to_store' => true,
            ],
            [
                'name' => 'Deck Box Premium',
                'slug' => 'deck-box-premium',
                'sku' => 'ACC-BOX-010',
                'barcode' => '750100000002',
                'description' => 'Deck box rigida para 100 cartas.',
                'short_description' => 'Proteccion premium para llevar tu deck completo.',
                'category' => 'Accesorios',
                'cost' => 85,
                'price' => 149,
                'stock' => 25,
                'active' => true,
                'featured' => true,
                'publish_to_store' => true,
            ],
            [
                'name' => 'Protector Matte x60',
                'slug' => 'protector-matte-x60',
                'sku' => 'ACC-SLEEVE-060',
                'barcode' => '750100000003',
                'description' => 'Fundas mate de alta resistencia.',
                'short_description' => 'Pack de 60 fundas para mazos competitivos.',
                'category' => 'Accesorios',
                'cost' => 45,
                'price' => 79,
                'stock' => 80,
                'active' => true,
                'featured' => false,
                'publish_to_store' => true,
            ],
        ];

        foreach ($products as $product) {
            $category = Category::query()->where('name', $product['category'])->first();

            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'uuid' => (string) Str::uuid(),
                    'category_id' => $category?->id,
                    ...$product,
                ],
            );
        }
    }
}
