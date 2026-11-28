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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // e.g. 1A, 2C, 5A, 6E
            $table->string('name', 50); // e.g. Kelas 1A, Kelas 5A
            $table->tinyInteger('grade_level'); // 1, 2, 3, 4, 5, 6
            $table->string('section', 5); // A, B, C, D, E
            $table->string('homeroom_teacher', 100)->nullable(); // Nama Wali Kelas
            $table->string('homeroom_nip', 30)->nullable();
            $table->integer('capacity')->default(32);
            $table->integer('total_students')->default(0);
            $table->string('academic_year', 20)->default('2025/2026');
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
