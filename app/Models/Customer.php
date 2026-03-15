<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Customer extends Model
{
    use Auditable, HasFactory;
    protected $fillable = [
        'type',
        'organization_id',
        'name',
        'phone',
        'email',
        'address',
        'id_number',
        'id_type',
        'nationality',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'is_active'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
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

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
