<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'document_path',
        // New profile fields
        'first_name', 'last_name', 'father_name', 'father_occupation', 'father_email', 'father_mobile',
        'mother_name', 'mother_occupation', 'mother_email', 'mother_mobile', 'gender', 'dob', 'emergency_phone',
        'religion', 'caste_category', 'caste', 'admission_quota', 'mother_tongue', 'nationality', 'marital_status',
        'blood_group', 'aadhaar_id', 'passport_no', 'admission_date',
        'present_state', 'present_city', 'present_address',
        'permanent_state', 'permanent_city', 'permanent_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is a warden
     */
    public function isWarden(): bool
    {
        return $this->role === 'warden';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get hostels managed by this warden
     */
    public function managedHostels()
    {
        return $this->hasMany(Hostel::class, 'warden_id');
    }

    /**
     * Get room applications submitted by this student
     */
    public function roomApplications()
    {
        return $this->hasMany(RoomApplication::class, 'student_id');
    }

    /**
     * Get room assignments for this student
     */
    public function roomAssignments()
    {
        return $this->hasMany(RoomAssignment::class, 'student_id');
    }

    /**
     * Get meal attendance records for this student
     */
    public function mealAttendances()
    {
        return $this->hasMany(MealAttendance::class, 'student_id');
    }

    /**
     * Get applications processed by this warden
     */
    public function processedApplications()
    {
        return $this->hasMany(RoomApplication::class, 'processed_by');
    }

    /**
     * Get meal attendance marked by this warden
     */
    public function markedAttendance()
    {
        return $this->hasMany(MealAttendance::class, 'marked_by');
    }

    /**
     * Get the hostel this student is assigned to (via room assignment)
     */
    public function hostel()
    {
        // If you have a direct hostel_id on users, use belongsTo(Hostel::class, 'hostel_id')
        // Otherwise, get via room assignment
        return $this->hasOneThrough(Hostel::class, RoomAssignment::class, 'student_id', 'id', 'id', 'hostel_id');
    }

    /**
     * Get all fees for this student
     */
    public function studentFees()
    {
        return $this->hasMany(\App\Models\StudentFee::class, 'student_id');
    }

    /**
     * Get the student profile for this user
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }
}
