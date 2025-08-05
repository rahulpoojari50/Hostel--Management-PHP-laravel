<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealAttendance extends Model
{
    use HasFactory;

    protected $table = 'meal_attendance';

    protected $fillable = [
        'student_id',
        'meal_id',
        'date',
        'meal_type',
        'status',
        'attendance_status',
        'hostel_id',
        'marked_by',
        'marked_at',
        'remarks',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
