<?php

namespace App\Repositories;

use App\Models\IngredientNotification;

class IngredientNotificationRepository implements IngredientNotificationRepositoryInterface
{
    public function find(int $id): IngredientNotification
    {
        return IngredientNotification::find($id);
    }

    public function create(array $data): IngredientNotification
    {
        return IngredientNotification::create($data);
    }
}