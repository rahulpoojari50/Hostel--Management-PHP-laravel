<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('phone')->nullable(); // Should be NOT NULL if required by validation
            $table->string('email')->nullable(); // Should be NOT NULL if required by validation
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('religion')->nullable();
            $table->string('caste_category')->nullable();
            $table->string('caste')->nullable();
            $table->string('admission_quota')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('nationality')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('aadhaar_id')->nullable();
            $table->string('passport_no')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('present_state')->nullable();
            $table->string('present_city')->nullable();
            $table->text('present_address')->nullable();
            $table->string('permanent_state')->nullable();
            $table->string('permanent_city')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
}; 