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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('gender', ['L', 'P'])->nullable()->after('avatar');
            $table->string('nuptk', 30)->nullable()->after('nip_nuptk')->index();
            $table->string('employment_status', 30)->nullable()->after('gender'); // PNS, PPPK, GTT, PTT, Honorer
            $table->string('position', 100)->nullable()->after('employment_status'); // Guru Kelas, Guru Mapel, Operator TU, Kepala Sekolah, dll.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'gender', 'nuptk', 'employment_status', 'position']);
        });
    }
};
