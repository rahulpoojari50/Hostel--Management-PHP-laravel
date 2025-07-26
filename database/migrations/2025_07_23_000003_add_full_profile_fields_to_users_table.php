<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Basic Info
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
            // Present Address
            $table->string('present_state')->nullable();
            $table->string('present_city')->nullable();
            $table->text('present_address')->nullable();
            // Permanent Address
            $table->string('permanent_state')->nullable();
            $table->string('permanent_city')->nullable();
            $table->text('permanent_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name','last_name','father_name','father_occupation','father_email','father_mobile',
                'mother_name','mother_occupation','mother_email','mother_mobile','gender','dob','emergency_phone',
                'religion','caste_category','caste','admission_quota','mother_tongue','nationality','marital_status',
                'blood_group','aadhaar_id','passport_no','admission_date',
                'present_state','present_city','present_address',
                'permanent_state','permanent_city','permanent_address',
            ]);
        });
    }
}; 