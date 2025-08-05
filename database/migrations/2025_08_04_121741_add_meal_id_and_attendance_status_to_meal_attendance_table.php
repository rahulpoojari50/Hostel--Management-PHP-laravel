<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('meal_id')->nullable()->after('student_id');
            $table->enum('attendance_status', ['present', 'absent'])->nullable()->after('status');
            $table->timestamp('marked_at')->nullable()->after('marked_by');
            
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->dropForeign(['meal_id']);
            $table->dropColumn(['meal_id', 'attendance_status', 'marked_at']);
        });
    }
};
