<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): Order
    {
        return Order::find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }
}