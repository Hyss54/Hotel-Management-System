<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenOrderItem extends Model
{
    protected $fillable = ['kitchen_order_id', 'item_name', 'quantity', 'unit_price', 'total', 'notes'];
    protected $casts = ['unit_price' => 'decimal:2', 'total' => 'decimal:2'];

    public function kitchenOrder(): BelongsTo
    {
        return $this->belongsTo(KitchenOrder::class);
    }
}
