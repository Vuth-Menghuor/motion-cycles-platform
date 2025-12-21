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
        // This migration consolidates all users table changes
        // It replaces: create_users_table, add_username_to_users_table, add_phone_to_users_table

        Schema::table('users', function (Blueprint $table) {
            // Add phone column if it doesn't exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove phone column
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
