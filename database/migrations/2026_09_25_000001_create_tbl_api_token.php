<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Token akses untuk API admin (aplikasi mobile).
 * Satu user bisa punya beberapa token (login dari beberapa perangkat).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_api_token', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('token', 80)->unique();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_api_token');
    }
};
