<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class KitchenOrder extends Model
{
    use Auditable;
    protected $fillable = [
        'booking_id',
        'customer_id',
        'created_by',
        'order_number',
        'notes',
        'total_amount',
        'status'
    ];

    protected $casts = ['total_amount' => 'decimal:2'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function items(): HasMany
    {
        return $this->hasMany(KitchenOrderItem::class);
    }
}
