<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function find(int $id): Order;

    public function create(array $data): Order;

}