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
//        $ingredients = [
//            ['name' => 'Beef',      'unit' => 'g', 'stock_quantity' => 20000 , 'initial_stock' => 20000],
//            ['name' => 'Cheese',    'unit' => 'g', 'stock_quantity' => 5000, 'initial_stock'=> 5000],
//            ['name' => 'Onion',     'unit' => 'g', 'stock_quantity' => 1000, 'initial_stock'=> 1000],
//        ];

        $ingredients = [
            ['name' => 'Beef',      'unit' => 'kg', 'stock_quantity' => 20 , 'initial_stock' => 20],
            ['name' => 'Cheese',    'unit' => 'kg', 'stock_quantity' => 5, 'initial_stock'=> 5],
            ['name' => 'Onion',     'unit' => 'kg', 'stock_quantity' => 1, 'initial_stock'=> 1],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}
