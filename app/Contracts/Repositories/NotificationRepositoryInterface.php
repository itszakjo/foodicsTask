<?php

namespace App\Contracts\Repositories;

use App\Models\Notification;

interface NotificationRepositoryInterface extends Repository
{
    /**
     * Find a model by ingredient.
     *
     * @param mixed $ingredientId
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function findByIngredient($ingredientId);
}