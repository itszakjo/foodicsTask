<?php

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Exceptions\SystemException;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
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
    public function createOrder(int $userId, array $items) : Order
    {
        try {
            DB::beginTransaction();

            // Create new order
            $order = $this->orderRepository->create(['user_id' => $userId]);

            // Process Order Items
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
     * Handles processing order items and updating stock.
     *
     * @param Order $order The order to process items for.
     * @param array $items The array of items to process.
     * @return void
     */
    public function processOrderItems($order,$items)
    {
        /*
         *  Handles order items and stock updates
         */
        foreach ($items['products'] as $item) {
            $product = $this->productRepository->find($item['product_id']);

            // assign order items
            $order->items()->create(['order_id'=> $order->id, 'product_id'=> $product->id ,'quantity' => $item['quantity']]);

            $this->inventoryService->updateStock($product, $item['quantity']);
        }
    }
}