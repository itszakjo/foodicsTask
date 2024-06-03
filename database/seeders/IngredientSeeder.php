<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Beef',      'unit' => 'g', 'stock_quantity' => 20000],
            ['name' => 'Cheese',    'unit' => 'g', 'stock_quantity' => 5000],
            ['name' => 'Onion',     'unit' => 'g', 'stock_quantity' => 1000],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}
