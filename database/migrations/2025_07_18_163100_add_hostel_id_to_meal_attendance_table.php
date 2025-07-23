<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->foreignId('hostel_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->dropColumn('hostel_id');
        });
    }
}; 