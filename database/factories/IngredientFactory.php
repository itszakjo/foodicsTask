<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $stock = $this->faker->numberBetween(1, 10);

        return [
            'name'                      => $this->faker->word,
            'unit'                      => 'kg',
            'stock_quantity'            => $stock ,
            'initial_stock'             => $stock
        ];
    }
}
