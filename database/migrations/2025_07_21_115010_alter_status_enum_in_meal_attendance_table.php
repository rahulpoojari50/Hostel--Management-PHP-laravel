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
            $table->enum('status', ['Taken', 'Skipped', 'On Leave', 'Holiday'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_attendance', function (Blueprint $table) {
            $table->enum('status', ['Taken', 'Skipped'])->change();
        });
    }
};
