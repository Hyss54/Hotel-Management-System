<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\Auditable;

class Booking extends Model
{
    use Auditable;
    protected $fillable = [
        'booking_number',
        'customer_id',
        'room_id',
        'created_by',
        'check_in_date',
        'check_out_date',
        'checked_in_at',
        'checked_out_at',
        'nights',
        'room_charge',
        'adults',
        'children',
        'status',
        'special_requests',
        'notes'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'room_charge' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kitchenOrders(): HasMany
    {
        return $this->hasMany(KitchenOrder::class);
    }

    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class);
    }

    public function barOrders(): HasMany
    {
        return $this->hasMany(BarOrder::class);
    }

    public function bill(): HasOne
    {
        return $this->hasOne(Bill::class);
    }
}
