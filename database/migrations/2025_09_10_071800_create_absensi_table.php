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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('absensi_id');
            $table->foreignId('siswa_id')->constrained('siswa', 'siswa_id');
            $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran', 'jadwal_id');
            $table->foreignId('dicatat_oleh_guru_id')->constrained('guru', 'guru_id');
            $table->date('tanggal_absensi');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alfa']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
