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
        Schema::create('absensi_harian', function (Blueprint $table) {
            $table->unsignedBigInteger('siswa_id');
            $table->foreign('siswa_id')->references('siswa_id')->on('siswa')->onDelete('cascade');
            $table->date('tanggal_absensi');
            $table->time('waktu_masuk');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir')->comment('status kehadiran');
            $table->timestamps();

            // Pastikan satu siswa hanya bisa absen sekali per hari
            $table->unique(['siswa_id', 'tanggal_absensi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_harian');
    }
};
