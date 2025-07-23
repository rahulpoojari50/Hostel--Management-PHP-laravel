<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'meal_type',
        'meal_date',
        'menu_description',
    ];

    protected $casts = [
        'meal_date' => 'date',
    ];

    /**
     * Get the hostel this meal belongs to
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get attendance records for this meal
     */
    public function attendance()
    {
        return $this->hasMany(MealAttendance::class);
    }

    /**
     * Get present students count
     */
    public function getPresentCountAttribute()
    {
        return $this->attendance()->where('attendance_status', 'present')->count();
    }

    /**
     * Get absent students count
     */
    public function getAbsentCountAttribute()
    {
        return $this->attendance()->where('attendance_status', 'absent')->count();
    }

    /**
     * Get total attendance count
     */
    public function getTotalAttendanceAttribute()
    {
        return $this->attendance()->count();
    }

    /**
     * Get attendance percentage
     */
    public function getAttendancePercentageAttribute()
    {
        $total = $this->total_attendance;
        if ($total === 0) {
            return 0;
        }
        
        return round(($this->present_count / $total) * 100, 2);
    }

    /**
     * Check if meal is for today
     */
    public function isToday(): bool
    {
        return $this->meal_date->isToday();
    }

    /**
     * Check if meal is for tomorrow
     */
    public function isTomorrow(): bool
    {
        return $this->meal_date->isTomorrow();
    }
}
