<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $fillable = [
        'bill_number',
        'booking_id',
        'customer_id',
        'created_by',
        'room_charge',
        'kitchen_charge',
        'laundry_charge',
        'bar_charge',
        'other_charges',
        'discount',
        'tax',
        'total_amount',
        'paid_amount',
        'balance',
        'status',
        'notes',
        'issued_at'
    ];

    protected $casts = [
        'room_charge' => 'decimal:2',
        'kitchen_charge' => 'decimal:2',
        'laundry_charge' => 'decimal:2',
        'bar_charge' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public static function generateBillNumber(): string
    {
        $latest = self::where('bill_number', 'like', 'BILL-GPH%')
            ->orderBy('bill_number', 'desc')
            ->first();

        if (!$latest) {
            return 'BILL-GPH000001';
        }

        $lastNumber = str_replace('BILL-GPH', '', $latest->bill_number);
        $nextNumber = intval($lastNumber) + 1;

        return 'BILL-GPH' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

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
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
