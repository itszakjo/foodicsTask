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

    /**
     * Create a new order for the specified user with the given items.
     *
     * @param int $userId The ID of the user placing the order.
     * @param array $items An array of items to be ordered, each item containing 'product_id' and 'quantity'.
     * @return Order The created order.
     * @throws SystemException If an error occurs while creating the order.
     */
    public function createOrder(int $userId, array $items): Order
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->create(['user_id' => $userId]);

            $this->processOrderItems($order,$items);

            DB::commit();

            return $order;
        }catch (\Exception $e) {
            // Rollback the transaction in case of an exception
            DB::rollBack();

            Log::error("Failed to create order: {$e->getMessage()}", [
                'user_id' => $userId,
                'products' => $items,
                'exception' => $e
            ]);

            throw new SystemException("Failed to create order: {$e->getMessage()}");
        }

    }

    /**
     * Process the items in the order.
     *
     * @param Order $order The order being processed.
     * @param array $items An array of items to be ordered, each item containing 'product_id' and 'quantity'.
     * @return void
     */
    public function processOrderItems($order,$items)
    {
        foreach ($items as $item) {
            $product = $this->productRepository->find($item['product_id']);

            $order->items()->attach($product, ['quantity' => $item['quantity']]);

            $this->inventoryService->updateStock($product, $item['quantity']);
        }
    }
}