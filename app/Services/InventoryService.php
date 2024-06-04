<?php

namespace App\Services;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Exceptions\SystemException;
use App\Models\Ingredient;
use App\Models\Product;
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

    /**
     * Update the stock of ingredients based on the given product and quantity.
     *
     * @param Product $product The product being ordered.
     * @param int $quantity The quantity of the product being ordered.
     * @throws SystemException If there is insufficient stock for any ingredient.
     */
    public function updateStock($product,$quantity)
    {
        foreach ($product->ingredients as $ingredient) {

            // Calculate the required quantity of the ingredient in base units
            $requiredQuantity = $this->convertToBaseUnit($ingredient->pivot->unit) * $ingredient->pivot->quantity * $quantity;

            // Check if there is sufficient stock for the ingredient
            if ($ingredient->stock_quantity < $requiredQuantity) {
                Log::info("Exception thrown for insufficient stock for ingredient: {$ingredient->name}");

                throw new SystemException("Insufficient stock for ingredient: {$ingredient->name}");
            }

            // Decrement the stock quantity of the ingredient
            $ingredient->decrement('stock_quantity', $requiredQuantity);

            // Check stock levels after updating the stock
            $this->checkStockLevels($ingredient);
        }
    }

    /**
     * Check the stock levels of an ingredient and send a notification to the merchant if it's below a certain threshold.
     *
     * @param Ingredient $ingredient The ingredient to check stock levels for.
     * @return void
     */
    public function checkStockLevels($ingredient)
    {
        $threshold = $ingredient->initial_stock * 0.5;

        if($ingredient->stock_quantity < $threshold && !$this->isIngredientNotified($ingredient)) {

            $this->notifyMerchant($ingredient);

            Log::info("Notification sent for low stock of ingredient: {$ingredient->name}");
        }
    }

    /**
     * Notifies the merchant about low stock of an ingredient.
     *
     * @param Ingredient $ingredient The ingredient with low stock
     * @return void
     */
    private function notifyMerchant($ingredient)
    {
        Notification::route('mail', config('mail.merchant_address'))->notify(new IngredientStockNotification($ingredient));

        $this->persistNotification($ingredient);
    }

    /**
     * Persists the notification about low stock of an ingredient in the database.
     *
     * @param Ingredient $ingredient The ingredient with low stock
     * @return void
     */
    private function persistNotification($ingredient)
    {
        $this->notificationRepository->create(['ingredient_id' => $ingredient->id]);
    }

    /**
     * Check if the ingredient has already been notified.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private function isIngredientNotified($ingredient)
    {
        return $this->notificationRepository->findByIngredient($ingredient->id);
    }

    /**
     * Convert the quantity of an ingredient to its base unit.
     *
     * @param  string  $unit
     * @return int
     */
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