<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->time('waktu_pulang')->nullable()->after('waktu_masuk');
            $table->string('foto_pulang')->nullable()->after('foto_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->dropColumn(['waktu_pulang', 'foto_pulang']);
        });
    }
};
