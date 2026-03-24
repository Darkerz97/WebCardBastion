<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Boosters',
                'description' => 'Sobres sellados y expansiones para juego competitivo.',
                'sort_order' => 1,
                'active' => true,
            ],
            [
                'name' => 'Accesorios',
                'description' => 'Micas, deck boxes, tapetes y equipo para jugadores.',
                'sort_order' => 2,
                'active' => true,
            ],
            [
                'name' => 'Coleccionables',
                'description' => 'Articulos premium y piezas especiales para la comunidad.',
                'sort_order' => 3,
                'active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [...$category, 'slug' => Str::slug($category['name'])],
            );
        }
    }
}
