<?php

namespace App\Providers;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\InventoryService;
use App\Services\OrderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class);

        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class);

        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class);

        $this->app->bind(
            InventoryServiceInterface::class,
            InventoryService::class
        );

        $this->app->bind(
            OrderServiceInterface::class,
            OrderService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
