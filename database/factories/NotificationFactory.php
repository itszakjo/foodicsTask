<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ingredient_id'          => $this->faker->numberBetween(1 , 10)
        ];
    }
}
