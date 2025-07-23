<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->json('fees')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn('fees');
        });
    }
}; 