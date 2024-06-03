<?php

namespace App\Repositories;


use App\Models\OrderItem;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function find(int $id): OrderItem
    {
        return OrderItem::find($id);
    }

    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }
}