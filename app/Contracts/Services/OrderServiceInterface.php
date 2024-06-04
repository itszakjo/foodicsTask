<?php

namespace App\Contracts\Services;


use App\Models\Order;

interface OrderServiceInterface
{
    public function createOrder(int $userId, array $products): Order;
}