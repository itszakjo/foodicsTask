<?php

namespace App\Repositories;

use App\Models\IngredientNotification;

interface IngredientNotificationInterface
{
    public function find(int $id): IngredientNotification;

    public function create(array $data): IngredientNotification;
}