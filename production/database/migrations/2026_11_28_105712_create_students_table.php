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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10)->unique()->index(); // 10 Digit NISN Resmi Kemdikbud
            $table->string('nis', 20)->nullable()->index(); // Nomor Induk Siswa Sekolah
            $table->string('name', 100)->index();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->string('birth_place', 60)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_relation', 40)->default('Orang Tua Kandung');
            $table->string('guardian_phone', 30)->nullable(); // Nomor WhatsApp Notifikasi
            $table->enum('status', ['active', 'graduated', 'moved', 'inactive'])->default('active')->index();
            $table->integer('point_balance')->default(100); // Saldo Poin Tatib (Default 100 Poin)
            $table->string('photo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
