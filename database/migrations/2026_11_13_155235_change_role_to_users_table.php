<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new role values to enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user', 'buyer', 'seller'])->default('buyer')->change();
        });

        // Step 2: Update existing 'user' roles to 'buyer'
        \App\Models\User::where('role', 'user')->update(['role' => 'buyer']);

        // Step 3: Remove old 'user' role from enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'seller', 'buyer'])->default('buyer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user', 'buyer', 'seller'])->default('buyer')->change();
        });

        \App\Models\User::where('role', 'seller')->update(['role' => 'buyer']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'buyer'])->default('buyer')->change();
        });
    }
};
