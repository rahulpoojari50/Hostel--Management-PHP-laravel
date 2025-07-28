<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'parent_mobile')) {
                $table->string('parent_mobile')->nullable();
            }
            if (!Schema::hasColumn('users', 'parent_email')) {
                $table->string('parent_email')->nullable();
            }
            if (!Schema::hasColumn('users', 'alternate_mobile')) {
                $table->string('alternate_mobile')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'parent_mobile')) {
                $table->dropColumn('parent_mobile');
            }
            if (Schema::hasColumn('users', 'parent_email')) {
                $table->dropColumn('parent_email');
            }
            if (Schema::hasColumn('users', 'alternate_mobile')) {
                $table->dropColumn('alternate_mobile');
            }
        });
    }
}; 