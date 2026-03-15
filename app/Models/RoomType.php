<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RoomType extends Model
{
    use Auditable, HasFactory;
    protected $fillable = ['name', 'description', 'price_per_night', 'max_guests', 'amenities', 'is_active'];

    protected $casts = [
        'amenities' => 'array',
        'price_per_night' => 'decimal:2',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
