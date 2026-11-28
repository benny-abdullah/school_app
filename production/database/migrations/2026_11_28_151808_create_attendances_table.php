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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('nisn', 20)->index();
            $table->date('date')->index();
            $table->string('checkin_time', 15)->nullable();
            $table->string('checkout_time', 15)->nullable();
            $table->enum('status', ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'])->default('Hadir');
            $table->string('late_level', 10)->nullable(); // 'T-0', 'T-1', 'T-2', 'T-3'
            $table->integer('late_minutes')->default(0);
            $table->enum('method', ['barcode_usb', 'camera_qr', 'manual_nisn'])->default('barcode_usb');
            $table->text('notes')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('gate_pass_active')->default(false);
            $table->string('gate_pass_reason')->nullable();
            $table->string('gate_pass_time', 15)->nullable();
            $table->timestamps();

            // 1 siswa hanya memiliki 1 catatan absensi per tanggal
            $table->unique(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
