<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained(table: 'siswa', column: 'siswa_id')->onDelete('cascade');
            $table->date('tanggal_izin');
            $table->text('alasan');
            $table->string('status')->default('pending'); // pending, disetujui, ditolak
            $table->foreignId('diapprove_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan_piket')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
