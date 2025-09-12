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
        // Add timestamps to role_user pivot table
        Schema::table('role_user', function (Blueprint $table) {
            $table->timestamps();
        });

        // Add timestamps to permission_role pivot table
        Schema::table('permission_role', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_user', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
