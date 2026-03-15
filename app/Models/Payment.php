<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'bill_id',
        'received_by',
        'reference',
        'amount',
        'method',
        'transaction_id',
        'notes',
        'paid_at'
    ];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
