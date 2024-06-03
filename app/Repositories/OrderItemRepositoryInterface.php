<?php

namespace App\Repositories;

use App\Models\OrderItem;

interface OrderItemRepositoryInterface
{
    public function find(int $id): OrderItem;

    public function create(array $data): OrderItem;

}