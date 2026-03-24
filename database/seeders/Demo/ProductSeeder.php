<?php

namespace Database\Seeders\Demo;

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
                'sku' => 'BST-ALPHA-001',
                'barcode' => '750100000001',
                'description' => 'Booster sellado para juego competitivo.',
                'category' => 'Boosters',
                'cost' => 60,
                'price' => 95,
                'stock' => 40,
                'active' => true,
            ],
            [
                'name' => 'Deck Box Premium',
                'sku' => 'ACC-BOX-010',
                'barcode' => '750100000002',
                'description' => 'Deck box rígida para 100 cartas.',
                'category' => 'Accesorios',
                'cost' => 85,
                'price' => 149,
                'stock' => 25,
                'active' => true,
            ],
            [
                'name' => 'Protector Matte x60',
                'sku' => 'ACC-SLEEVE-060',
                'barcode' => '750100000003',
                'description' => 'Fundas mate de alta resistencia.',
                'category' => 'Accesorios',
                'cost' => 45,
                'price' => 79,
                'stock' => 80,
                'active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                ['uuid' => (string) Str::uuid(), ...$product]
            );
        }
    }
}
