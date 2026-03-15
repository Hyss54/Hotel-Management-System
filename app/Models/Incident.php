<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Incident extends Model
{
    use Auditable, HasFactory;
    protected $fillable = [
        'booking_id',
        'room_id',
        'customer_id',
        'title',
        'description',
        'severity',
        'status',
        'reported_by'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
