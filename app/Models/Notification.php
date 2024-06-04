<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['ingredient_id'];

    public function ingredient() : BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}

