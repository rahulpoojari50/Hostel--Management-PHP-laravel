<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'type',
        'capacity',
        'price_per_month',
        'total_rooms',
        'facilities',
    ];

    protected $casts = [
        'facilities' => 'array',
        'price_per_month' => 'decimal:2',
    ];

    protected $appends = ['available_rooms'];

    /**
     * Dynamically calculate available rooms: total_rooms - active assignments
     */
    public function getAvailableRoomsAttribute()
    {
        if (is_null($this->total_rooms)) {
            return 0;
        }
        $assigned = $this->roomAssignments()->count();
        // Ignore Room records, always use total_rooms - assigned
        return max(0, $this->total_rooms - $assigned);
    }

    /**
     * Get the hostel this room type belongs to
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get rooms of this type
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get applications for this room type
     */
    public function roomApplications()
    {
        return $this->hasMany(RoomApplication::class);
    }

    /**
     * Get available rooms count
     */
    public function getAvailableRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'available')->count();
    }

    /**
     * Get occupied rooms count
     */
    public function getOccupiedRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'occupied')->count();
    }

    /**
     * Get maintenance rooms count
     */
    public function getMaintenanceRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'maintenance')->count();
    }

    /**
     * Get all room assignments for this room type (via rooms)
     */
    public function roomAssignments()
    {
        return $this->hasManyThrough(
            \App\Models\RoomAssignment::class,
            \App\Models\Room::class,
            'room_type_id', // Foreign key on Room
            'room_id',      // Foreign key on RoomAssignment
            'id',           // Local key on RoomType
            'id'            // Local key on Room
        )->where('room_assignments.status', 'active');
    }
}
