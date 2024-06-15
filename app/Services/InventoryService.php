<?php

namespace App\Services;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Exceptions\SystemException;
use App\Models\Ingredient;
use App\Models\Product;
use App\Notifications\IngredientStockNotification;
use Illuminate\Database\QueryException;
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
     * @return bool
     * @throws SystemException if stock is insufficient or notification failed to persist
     */
    public function updateStock($product,$quantity)
    {
        Log::debug("Updating stock for product '{$product->name}' (ID: {$product->id}), quantity: {$quantity}");

        foreach ($product->ingredients as $ingredient) {

            // Calculate the required quantity of the ingredient in base units
            $requiredQuantity = $this->getUnitRate($ingredient->unit) * $ingredient->pivot->quantity * $quantity;

            // Check if there is sufficient stock for the ingredient
            if ($ingredient->stock_quantity < $requiredQuantity) {
                Log::info("Exception thrown for insufficient stock for ingredient '{$ingredient->name}' (ID: {$ingredient->id}).");

                throw new SystemException("Insufficient stock for ingredient: {$ingredient->name}");
            }

            // Decrement the stock quantity of the ingredient
            $ingredient->decrement('stock_quantity', $requiredQuantity);

            // Check stock levels after updating the stock
            $this->checkStockLevels($ingredient);
        }

        Log::info("Successfully updated stock for product '{$product->name}' (ID: {$product->id})");

        return true;
    }

    /**
     * Check the stock levels of an ingredient and send a notification to the merchant if it's below a certain threshold.
     *
     * @param Ingredient $ingredient The ingredient to check stock levels for.
     * @return void
     * @throws SystemException
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
     * @throws SystemException
     */
    public function notifyMerchant($ingredient)
    {
        Notification::route('mail', config('mail.merchant_address'))->notify(new IngredientStockNotification($ingredient));

        $this->persistNotification($ingredient);
    }

    /**
     * Persists the notification about low stock of an ingredient in the database.
     *
     * @param Ingredient $ingredient The ingredient with low stock
     * @return void
     * @throws SystemException
     */
    public function persistNotification($ingredient)
    {
        try {
            $this->notificationRepository->create(['ingredient_id' => $ingredient->id]);

            Log::info("persisted notification for ingredient : {$ingredient->name} Successfully");
        } catch (\Exception $e) {
            Log::error("Error persisting notification for ingredient '{$ingredient->name}': ID: [{$ingredient->id}] " . $e->getMessage());

            throw new SystemException("Error persisting notification for ingredient '{$ingredient->name}'");
        }
    }

    /**
     * Check if the ingredient has already been notified.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function isIngredientNotified($ingredient)
    {
        return $this->notificationRepository->findByIngredient($ingredient->id);
    }

    /**
     * Get the unit rate
     *
     * @param  string  $unit
     * @return int
     */
    public function getUnitRate($unit)
    {
        //use config('units.conversion_rates') if you need to use this array for other functions
        $conversionRates = [
            'g'     => 1000,
            'kg'    => 1,
            'ml'    => 1000,
            'l'     => 1,
        ];

        if (!isset($conversionRates[$unit])) {
            Log::error("Failed to find: '{$unit}' in conversation rates");

            throw new \InvalidArgumentException("Unsupported Unit: '{$unit}'");
        }

        return $conversionRates[$unit];
    }
}