<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'laundry_item_id',
        'from_department_id',
        'to_department_id',
        'quantity',
        'transaction_type',
        'status',
        'notes',
        'created_by'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(LaundryItem::class, 'laundry_item_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
