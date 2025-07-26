<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name', 'last_name', 'father_name', 'father_occupation', 'father_email', 'father_mobile',
        'mother_name', 'mother_occupation', 'mother_email', 'mother_mobile', 'gender', 'dob', 'emergency_phone',
        'religion', 'caste_category', 'caste', 'admission_quota', 'mother_tongue', 'nationality', 'marital_status',
        'blood_group', 'aadhaar_id', 'passport_no', 'admission_date',
        'present_state', 'present_city', 'present_address',
        'permanent_state', 'permanent_city', 'permanent_address',
        'document_path',
        'email', 'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 