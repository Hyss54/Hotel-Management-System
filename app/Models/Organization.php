<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'registration_number', 'notes', 'is_active'];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
