<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function find($id): Product
    {
        return Product::find($id);
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }
}