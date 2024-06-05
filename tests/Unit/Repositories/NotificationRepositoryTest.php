<?php

namespace Tests\Unit\Repositories;

use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Product;
use App\Repositories\NotificationRepository;
use App\Repositories\ProductRepository;
use Database\Factories\IngredientFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NotificationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_notification_by_ingredient()
    {
        // Arrange
        $ingredient             = Ingredient::factory()->create();
        $notification           = Notification::factory()->create(['ingredient_id' => $ingredient->id]);
        $notificationRepository = new NotificationRepository();

        // Act
        $actualNotification = $notificationRepository->find($notification->id);

        // Assert
        $this->assertEquals($notification->id, $actualNotification->id);
        $this->assertEquals($notification->ingredient_id, $actualNotification->ingredient_id);
    }
}
