<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'stock_quantity'];

    protected $initial_stock = 0;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ingredient) {
            $ingredient->initial_stock = $ingredient->stock_quantity;
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
