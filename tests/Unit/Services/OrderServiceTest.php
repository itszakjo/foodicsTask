<?php

namespace Tests\Unit;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating an order.
     *
     * This test ensures that an order is created successfully when valid data provided and order items are assigned correctly.
     */
    public function test_create_order()
    {
        // Arrange
        $user          = User::factory()->create();
        $userId        = $user->id;

        $product1      = Product::factory()->create();

        $ingredient1   = Ingredient::factory()->create();
        $ingredient2   = Ingredient::factory()->create();
        $ingredient3   = Ingredient::factory()->create();

        $product1->ingredients()->attach($ingredient1, ['quantity' => 0.150]);
        $product1->ingredients()->attach($ingredient2, ['quantity' => 0.20]);
        $product1->ingredients()->attach($ingredient3, ['quantity' => 0.30]);

        $data = [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
            ],
        ];

        // Mock OrderRepository
        $orderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $orderRepository->shouldReceive('create')
            ->once()
            ->with(['user_id' => $userId])
            ->andReturnUsing(function ($attributes) {
                // Simulate the creation of an order
                return Order::create($attributes);
            });

        // Mock ProductRepository
        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $productRepository->shouldReceive('find')
            ->once()
            ->andReturn($product1);

        // Mock InventoryService
        $inventoryService = Mockery::mock(InventoryServiceInterface::class);
        $inventoryService->shouldReceive('updateStock')
            ->once()
            ->with($product1, 2); // Ensure that updateStock is called with correct arguments


        // Mock DB facade
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never(); // Expectation for rollback

        // Mock Log facade
        Log::shouldReceive('error')->never();

        // Create an instance of the OrderService
        $orderService = new OrderService($orderRepository, $productRepository, $inventoryService);

        //Act
        $order = $orderService->createOrder($userId, $data);

        //Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($userId, $order->user_id);
    }

    /**
     * Test processing order items.
     *
     * This test ensures that order items are created and correctly assigned to the order and stock is updated.
     */
    public function test_process_order_items()
    {
        // Arrange
        $user          = User::factory()->create();
        $userId        = $user->id;

        $product1      = Product::factory()->create();

        $ingredient1   = Ingredient::factory()->create();
        $ingredient2   = Ingredient::factory()->create();
        $ingredient3   = Ingredient::factory()->create();

        $product1->ingredients()->attach($ingredient1, ['quantity' => 0.150]);
        $product1->ingredients()->attach($ingredient2, ['quantity' => 0.20]);
        $product1->ingredients()->attach($ingredient3, ['quantity' => 0.30]);

        $order = Order::factory()->create();

        $data = [
            'products' => [
                ['product_id' => $product1->id, 'quantity' => 2],
            ],
        ];

        // Mock ProductRepository
        $productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $productRepository->shouldReceive('find')
            ->once()
            ->with($product1->id) // Ensure that find is called with correct product ID
            ->andReturn($product1);

        // Mock InventoryService
        $inventoryService = Mockery::mock(InventoryServiceInterface::class);
        $inventoryService->shouldReceive('updateStock')
            ->once()
            ->with($product1, 2); // to ensure that updateStock is called with correct product object and quantity


        // Create an instance of the OrderService
        $orderService = new OrderService(Mockery::mock(OrderRepositoryInterface::class), $productRepository, $inventoryService);

        // Act
        $orderService->processOrderItems($order, $data);

        // Assert
        // Check if order items are created
        $this->assertCount(1, $order->items);

        foreach ($order->items as $item) {
            $this->assertInstanceOf(OrderItem::class, $item);
        }
    }

}
