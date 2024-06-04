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
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function find($id): Order
    {
        return Order::find($id);
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }
}