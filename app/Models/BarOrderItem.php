<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarOrderItem extends Model
{
    protected $fillable = [
        'bar_order_id',
        'bar_item_id',
        'item_name',
        'quantity',
        'unit_price',
        'total',
        'notes'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(BarOrder::class, 'bar_order_id');
    }

    public function barItem(): BelongsTo
    {
        return $this->belongsTo(BarItem::class);
    }
}
