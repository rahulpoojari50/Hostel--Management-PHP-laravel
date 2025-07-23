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
        Schema::table('hostels', function (Blueprint $table) {
            $table->integer('room_1_share')->default(0);
            $table->integer('room_2_share')->default(0);
            $table->integer('room_3_share')->default(0);
            $table->json('menu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn(['room_1_share', 'room_2_share', 'room_3_share', 'menu']);
        });
    }
};
