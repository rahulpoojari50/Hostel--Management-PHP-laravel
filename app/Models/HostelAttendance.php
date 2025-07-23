<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelAttendance extends Model
{
    use HasFactory;

    protected $table = 'hostel_attendance';

    protected $fillable = [
        'student_id',
        'hostel_id',
        'date',
        'status',
        'marked_by',
        'remarks',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
} 