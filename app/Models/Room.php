<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'current_occupants',
        'max_occupants',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the hostel this room belongs to
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get the room type of this room
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get room assignments for this room
     */
    public function roomAssignments()
    {
        return $this->hasMany(RoomAssignment::class);
    }

    /**
     * Get current occupants of this room
     */
    public function currentOccupants()
    {
        return $this->hasManyThrough(
            User::class,
            RoomAssignment::class,
            'room_id',
            'id',
            'id',
            'student_id'
        )->where('room_assignments.status', 'active');
    }

    /**
     * Check if room is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->current_occupants < $this->max_occupants;
    }

    /**
     * Check if room is full
     */
    public function isFull(): bool
    {
        return $this->current_occupants >= $this->max_occupants;
    }

    /**
     * Check if room is under maintenance
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacityAttribute()
    {
        return $this->max_occupants - $this->current_occupants;
    }
}
