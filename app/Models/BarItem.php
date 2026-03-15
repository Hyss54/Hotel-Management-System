<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BarItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'expiry_date',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'expiry_date' => 'date',
    ];

    /**
     * Returns true if the item's expiry date is in the past.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    /**
     * Returns true if the item expires within the next $days days.
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if ($this->expiry_date === null || $this->isExpired()) {
            return false;
        }
        return $this->expiry_date->lte(Carbon::today()->addDays($days));
    }

    public function orderItems()
    {
        return $this->hasMany(BarOrderItem::class);
    }
}
