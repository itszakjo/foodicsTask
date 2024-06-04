<?php

namespace App\Services;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Exceptions\SystemException;
use App\Notifications\IngredientStockNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class InventoryService implements InventoryServiceInterface
{
    private  $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function updateStock($product,$quantity)
    {
        foreach ($product->ingredients as $ingredient) {
            $requiredQuantity = $this->convertToBaseUnit($ingredient->pivot->unit) * $ingredient->pivot->quantity * $quantity;

            if ($ingredient->stock_quantity < $requiredQuantity) {
                Log::info("Exception thrown for insufficient stock for ingredient: {$ingredient->name}");

                throw new SystemException("Insufficient stock for ingredient: {$ingredient->name}");
            }

            $ingredient->decrement('stock_quantity', $requiredQuantity);

            $this->checkStockLevels($ingredient);
        }
    }

    public function checkStockLevels($ingredient)
    {
        $threshold = $ingredient->initial_stock * 0.5;

        if($ingredient->stock_quantity < $threshold && !$this->isIngredientNotified($ingredient)) {

            $this->notifyMerchant($ingredient);

            Log::info("Notification sent for low stock of ingredient: {$ingredient->name}");
        }
    }

    private function notifyMerchant($ingredient)
    {
        Notification::route('mail', config('mail.merchant_address'))->notify(new IngredientStockNotification($ingredient));

        $this->persistNotification($ingredient);
    }

    private function persistNotification($ingredient)
    {
        $this->notificationRepository->create(['ingredient_id' => $ingredient->id]);
    }

    private function isIngredientNotified($ingredient)
    {
        return $this->notificationRepository->findByIngredient($ingredient->id);
    }

    private function convertToBaseUnit($unit)
    {
        $conversionRates = [
            'g'     => 1000,
            'kg'    => 1,
            'ml'    => 1000,
            'l'     => 1,
        ];

        return $conversionRates[$unit] ?? 1;
    }
}