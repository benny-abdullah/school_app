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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150)->nullable();
            $table->string('user_role', 50)->nullable();
            $table->string('action', 100)->index(); // misal: LOGIN, LOGOUT, CREATE, UPDATE, DELETE, RESET_PASSWORD, ROTASI_ROMBEL
            $table->string('module', 100)->index(); // misal: Autentikasi, Master GTK, Manajemen User, Master Siswa, Master Rombel, Profil
            $table->text('description');
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
