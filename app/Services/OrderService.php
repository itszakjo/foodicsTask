<?php

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Exceptions\SystemException;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    private $orderRepository;
    private $productRepository;
    private $inventoryService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        InventoryServiceInterface $inventoryService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->inventoryService = $inventoryService;
    }

    public function createOrder(int $userId, array $items): Order
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->create(['user_id' => $userId]);

            $this->processOrderItems($order,$items);

            DB::commit();

            return $order;
        }catch (\Exception $e) {
            DB::rollBack();

            Log::error("Failed to create order: {$e->getMessage()}", [
                'user_id' => $userId,
                'products' => $items,
                'exception' => $e
            ]);

            throw new SystemException("Failed to create order: {$e->getMessage()}");
        }

    }

    public function processOrderItems($order,$items)
    {
        foreach ($items as $item) {
            $product = $this->productRepository->find($item['product_id']);

            $order->items()->attach($product, ['quantity' => $item['quantity']]);

            $this->inventoryService->updateStock($product, $item['quantity']);
        }
    }
}