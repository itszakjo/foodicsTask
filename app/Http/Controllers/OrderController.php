<?php

namespace App\Http\Controllers;

use App\Contracts\Services\OrderServiceInterface;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order.
     *
     * @param OrderRequest $orderRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(OrderRequest $orderRequest)
    {
        try {
            $order = $this->orderService->createOrder(auth()->user()->id,  $orderRequest->validated());

            return response()->json(['message' => 'Order placed successfully', 'order_number' => $order->id] , 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
