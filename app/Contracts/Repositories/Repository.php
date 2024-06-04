<?php

namespace App\Contracts\Repositories;

/**
 * Interface Repository.
 */
interface Repository
{
    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function find($id);


    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public function create(array $data);
}
