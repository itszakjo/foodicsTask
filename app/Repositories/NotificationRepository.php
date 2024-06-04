<?php

namespace App\Repositories;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Models\Notification;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function find($id): Notification
    {
        return Notification::find($id);
    }


    /**.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }


    /**
     * Find a model by ingredient.
     *
     * @param mixed $ingredientId
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function findByIngredient($ingredientId): Notification
    {
        return Notification::where('ingredient_id' , $ingredientId)->first();
    }
}