<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\NotificationRepositoryInterface;

use App\Exceptions\SystemException;
use App\Models\Ingredient;
use App\Models\Product;
use App\Notifications\IngredientStockNotification;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test updating stock when there is sufficient quantity.
     */
    public function test_update_stock_sufficient_quantity()
    {
        // Arrange
        $product1      = Product::factory()->create();
        $quantity      = 2;
        $ingredient1 = Ingredient::factory()->create(['unit'=> 'kg', 'stock_quantity' => 20 , 'initial_stock' => 20 ]);
        $ingredient2 = Ingredient::factory()->create(['unit'=> 'kg',  'stock_quantity' => 5, 'initial_stock' => 5 ]);
        $ingredient3 = Ingredient::factory()->create(['unit'=> 'kg',  'stock_quantity' => 2 ,'initial_stock' => 2 ]);

        $product1->ingredients()->attach($ingredient1, ['quantity' => 0.150]);
        $product1->ingredients()->attach($ingredient2, ['quantity' => 0.20]);
        $product1->ingredients()->attach($ingredient3, ['quantity' => 0.30]);


        // Mock NotificationRepositoryInterface
        Notification::fake();

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($this->app->make(NotificationRepositoryInterface::class));

        // Act
        $inventoryService->updateStock($product1, $quantity);

        // Assert
        $this->assertEquals(19.7, $ingredient1->refresh()->stock_quantity, '', 0.001);
        $this->assertEquals(4.6, $ingredient2->refresh()->stock_quantity, '', 0.001);
        $this->assertEquals(1.4, $ingredient3->refresh()->stock_quantity, '', 0.001);

        Notification::assertNothingSent();
    }

    /**
     * Test updating stock when there is insufficient quantity.
     */
    public function test_update_stock_insufficient_quantity()
    {
        // Arrange
        $this->expectException(SystemException::class);

        $product1      = Product::factory()->create();
        $quantity      = 2000000;
        $ingredient1 = Ingredient::factory()->create(['unit'=> 'kg', 'stock_quantity' => 20 , 'initial_stock' => 20 ]);
        $ingredient2 = Ingredient::factory()->create(['unit'=> 'kg',  'stock_quantity' => 5, 'initial_stock' => 5 ]);
        $ingredient3 = Ingredient::factory()->create(['unit'=> 'kg',  'stock_quantity' => 2 ,'initial_stock' => 2 ]);

        $product1->ingredients()->attach($ingredient1, ['quantity' => 0.150]);
        $product1->ingredients()->attach($ingredient2, ['quantity' => 0.20]);
        $product1->ingredients()->attach($ingredient3, ['quantity' => 0.30]);

        // Mock NotificationRepositoryInterface
        Notification::fake();

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($this->app->make(NotificationRepositoryInterface::class));

        // Act
        $inventoryService->updateStock($product1, $quantity);

        // Assert
        Notification::assertNothingSent();
    }

    /**
     * Test checking stock levels when stock is below the threshold.
     */
    public function test_check_stock_levels_below_threshold()
    {
        // Arrange
        $ingredient = Ingredient::factory()->create(['stock_quantity' => 9, 'initial_stock' => 20]);

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($this->app->make(NotificationRepositoryInterface::class));

        // Act
        $inventoryService->checkStockLevels($ingredient);

        // Assert
        $this->assertTrue($ingredient->fresh()->stock_quantity < $ingredient->initial_stock * 0.5); // Verify threshold calculation

    }

    /**
     * Test checking stock levels when stock is above the threshold.
     */
    public function test_check_stock_levels_above_threshold()
    {
        // Arrange
        $ingredient = Ingredient::factory()->create(['stock_quantity' => 15, 'initial_stock' => 20]);

        // Mock NotificationRepositoryInterface
        Notification::fake();

        // Mock Log facade
        Log::shouldReceive('info')->never();

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($this->app->make(NotificationRepositoryInterface::class));

        // Act
        $inventoryService->checkStockLevels($ingredient);

        // Assert
        Notification::assertNothingSent();
    }

    /**
     * Test checking if an ingredient has a notification if stock goes below 50 and notification sent.
     */
    public function test_check_ingredient_has_notification_stock_levels_below_threshold()
    {
        // Arrange
        $ingredient = Ingredient::factory()->create(['stock_quantity' => 9, 'initial_stock' => 20]);

        // Mock NotificationRepositoryInterface
        Notification::fake();

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($this->app->make(NotificationRepositoryInterface::class));

        // Act
        $inventoryService->checkStockLevels($ingredient);

        // Act
        $notification = $inventoryService->isIngredientNotified($ingredient);

        Notification::assertSentOnDemandTimes(IngredientStockNotification::class, 1);

        // Assert
        $this->assertInstanceOf(\App\Models\Notification::class, $notification);
    }

    /**
     * Test checking if a notification sent only once if stock is below threshold
     */
    public function test_notification_sent_only_once_below_threshold()
    {
        // Arrange
        $ingredient = Ingredient::factory()->create(['stock_quantity' => 40, 'initial_stock' => 100]);

        // Mock NotificationRepositoryInterface
        Notification::fake();

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($this->app->make(NotificationRepositoryInterface::class));

        // Act
        // First check, should not send notification
        $inventoryService->checkStockLevels($ingredient);

        // Second check, should send notification
        $inventoryService->checkStockLevels($ingredient);

        // third check, should send notification
        $inventoryService->checkStockLevels($ingredient);

        // Assert
        Notification::assertSentOnDemandTimes(IngredientStockNotification::class, 1);
    }


    /**
     * Test checking if a notification sent and is persisted on the notifications table
     */
    public function test_notify_merchant()
    {
        // Arrange
        $ingredient = Ingredient::factory()->create();

        // Mock NotificationRepositoryInterface
        $notificationRepository = Mockery::mock(NotificationRepositoryInterface::class);
        $notificationRepository->shouldReceive('create')->once();

        // Mock Notification facade
        Notification::fake();

        // Create an instance of the InventoryService
        $inventoryService = new InventoryService($notificationRepository);

        // Act
        $inventoryService->notifyMerchant($ingredient);

        // Assert ( using this instead of assertSentTo ) since we don't have any notifiable object so , as we just need to ensure there is one notification sent
        Notification::assertSentOnDemandTimes(IngredientStockNotification::class, 1);

        // Verify that the notification is persisted
        $notificationRepository->shouldHaveReceived('create')->once();
    }
}
