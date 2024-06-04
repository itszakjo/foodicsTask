<?php

namespace App\Contracts\Services;


interface InventoryServiceInterface
{
    public function updateStock($product, $quantity);
    public function checkStockLevels($ingredient);
}