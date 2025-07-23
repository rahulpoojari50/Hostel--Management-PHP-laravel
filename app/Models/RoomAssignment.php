<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'room_id',
        'assigned_date',
        'checkout_date',
        'status',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'checkout_date' => 'date',
        'status' => 'string',
    ];

    /**
     * Get the student assigned to this room
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the room this assignment is for
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Check if assignment is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if assignment is checked out
     */
    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }

    /**
     * Check out the student
     */
    public function checkOut(): void
    {
        $this->update([
            'status' => 'checked_out',
            'checkout_date' => now(),
        ]);

        // Update room occupancy
        $this->room->decrement('current_occupants');
        
        // Update room status if no occupants
        if ($this->room->current_occupants <= 0) {
            $this->room->update(['status' => 'available']);
        }
    }

    /**
     * Get the hostel through room
     */
    public function hostel()
    {
        return $this->hasOneThrough(
            Hostel::class,
            Room::class,
            'id',
            'id',
            'room_id',
            'hostel_id'
        );
    }
}
