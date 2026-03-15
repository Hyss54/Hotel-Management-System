<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class HousekeepingTask extends Model
{
    use Auditable;
    protected $fillable = ['room_id', 'task_type', 'status', 'assigned_to', 'notes', 'created_by'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
