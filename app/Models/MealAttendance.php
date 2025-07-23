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
        'date',
        'meal_type',
        'status',
        'hostel_id',
        'marked_by',
        'remarks',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
