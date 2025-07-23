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
        Schema::table('room_applications', function (Blueprint $table) {
            // SQLite does not support dropping foreign keys directly, so this is a placeholder.
            // In MySQL/Postgres, you would use: $table->dropForeign(['room_type_id']);
            // For SQLite, you may need to recreate the table without the foreign key if you still get errors.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for SQLite, or re-add the foreign key if needed
    }
};
