<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_applications', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('room_applications', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
}; 