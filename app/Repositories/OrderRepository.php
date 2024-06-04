<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{

    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     *
     * @return Order
     */
    public function find($id): Order
    {
        return Order::find($id);
    }

    /**
     * @param array $data
     *
     * @return Order
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }
}