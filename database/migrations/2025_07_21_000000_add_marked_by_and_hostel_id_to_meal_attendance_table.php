<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('marked_by')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->dropColumn('marked_by');
        });
    }
}; 