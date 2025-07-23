<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'address',
        'description',
        'warden_id',
        'status',
        'room_1_share',
        'room_2_share',
        'room_3_share',
        'menu',
    ];

    protected $casts = [
        'status' => 'string',
        'menu' => 'array',
        'fees' => 'array',
    ];

    /**
     * Get the warden who manages this hostel
     */
    public function warden()
    {
        return $this->belongsTo(User::class, 'warden_id');
    }

    /**
     * Get room types available in this hostel
     */
    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    /**
     * Get rooms in this hostel
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get room applications for this hostel
     */
    public function roomApplications()
    {
        return $this->hasMany(RoomApplication::class);
    }

    /**
     * Get meals served in this hostel
     */
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * Get students assigned to rooms in this hostel
     */
    public function students()
    {
        return $this->hasManyThrough(User::class, RoomApplication::class, 'hostel_id', 'id', 'id', 'student_id')->where('room_applications.status', 'approved');
    }

    /**
     * Check if hostel is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the total fees for a given room type (or for the hostel if not per room type)
     */
    public function getTotalFeesForRoomType($roomTypeId = null)
    {
        // For now, sum all fees in the 'fees' array (if set)
        $fees = $this->fees ?? [];
        $sum = 0;
        foreach ($fees as $fee) {
            if (is_array($fee) && isset($fee['amount'])) {
                $sum += floatval($fee['amount']);
            } elseif (is_numeric($fee)) {
                $sum += floatval($fee);
            }
        }
        return $sum;
    }
}
