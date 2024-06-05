<?php

namespace Tests\Unit\Repositories;

use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Database\Factories\IngredientFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order()
    {
        // Arrange
        $product = Product::factory()->create(); // Create a product in the database
        $user    = User::factory()->create(); // Create a user in the database

        $orderData = [
            'user_id' => $user->id
        ];

        // Act
        $createdOrder = $this->createOrderWithProduct($orderData, $product);

        // Assert
        $this->assertInstanceOf(Order::class, $createdOrder);
        $this->assertDatabaseHas('orders', $orderData); // Check if the order was stored in the database
    }

    private function createOrderWithProduct(array $orderData, Product $product): Order
    {
        $orderRepository = new OrderRepository();

        // Create the order
        $createdOrder = $orderRepository->create($orderData);

        // Add the product to the order items
        $createdOrder->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        return $createdOrder;
    }
}
