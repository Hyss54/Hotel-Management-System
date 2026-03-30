<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class LaundryItem extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'description',
        'stock_quantity',
        'dirty_quantity',
        'in_housekeeping_quantity',
        'unit',
        'min_stock_level',
        'is_active'
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'dirty_quantity' => 'integer',
        'in_housekeeping_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'laundry_item_id');
    }
}
