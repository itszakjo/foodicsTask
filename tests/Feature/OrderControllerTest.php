<?php

namespace Tests\Feature;

use App\Contracts\Repositories\NotificationRepositoryInterface;

use App\Exceptions\SystemException;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

use App\Notifications\IngredientStockNotification;
use App\Services\InventoryService;
use App\Services\OrderService;
use Database\Factories\IngredientFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test updating stock when there is sufficient quantity.
     */
    public function test_order_creation()
    {
        // Arrange
        $product     = Product::factory()->create();
        $ingredient1 = Ingredient::factory()->create(['unit' => 'kg', 'stock_quantity' => 20, 'initial_stock' => 20]);
        $ingredient2 = Ingredient::factory()->create(['unit' => 'kg', 'stock_quantity' => 5, 'initial_stock' => 5]);
        $ingredient3 = Ingredient::factory()->create(['unit' => 'kg', 'stock_quantity' => 2, 'initial_stock' => 2]);

        $product->ingredients()->attach($ingredient1, ['quantity' => 0.150]);
        $product->ingredients()->attach($ingredient2, ['quantity' => 0.20]);
        $product->ingredients()->attach($ingredient3, ['quantity' => 0.30]);

        // Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        // Send authenticated request to create an order
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/order/create', [
                'products' => [
                    ['product_id' => $product->id, 'quantity' => 2]
                ]
            ]);

        // Assert response status and order creation
        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', ['id' => 1]);
        $this->assertDatabaseHas('product_ingredients', ['product_id' => $product->id, 'ingredient_id' => $ingredient1->id]);
        $this->assertDatabaseHas('product_ingredients', ['product_id' => $product->id, 'ingredient_id' => $ingredient2->id]);
        $this->assertDatabaseHas('product_ingredients', ['product_id' => $product->id, 'ingredient_id' => $ingredient2->id]);

        $this->assertEquals(19.7, $ingredient1->refresh()->stock_quantity, '', 0.001);
        $this->assertEquals(4.6, $ingredient2->refresh()->stock_quantity, '', 0.001);
        $this->assertEquals(1.4, $ingredient3->refresh()->stock_quantity, '', 0.001);
    }
}
