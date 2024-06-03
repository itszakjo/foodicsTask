<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            'Burger' => [
                ['ingredient' => 'Beef', 'quantity' => 150], // 150g Beef
                ['ingredient' => 'Cheese', 'quantity' => 30], // 30g Cheese
                ['ingredient' => 'Onion', 'quantity' => 20], // 20g Onion
            ]
        ];

        foreach ($products as $productName => $ingredients) {
            $product = Product::where('name', $productName)->first();

            foreach ($ingredients as $ingredientData) {
                $ingredient = Ingredient::where('name', $ingredientData['ingredient'])->first();
                $product->ingredients()->attach($ingredient->id, ['quantity' => $ingredientData['quantity']]);
            }
        }
    }
}
