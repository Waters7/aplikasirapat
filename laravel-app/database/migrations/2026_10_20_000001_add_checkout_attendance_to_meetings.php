<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rapat', 'kode_presensi_pulang')) {
            Schema::table('rapat', function (Blueprint $table) {
                $table->string('kode_presensi_pulang', 20)->nullable();
                $table->dateTime('presensi_pulang_kedaluwarsa')->nullable();
            });
        }

        if (!Schema::hasColumn('kehadiran', 'waktu_pulang')) {
            Schema::table('kehadiran', function (Blueprint $table) {
                $table->dateTime('waktu_pulang')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kehadiran', 'waktu_pulang')) {
            Schema::table('kehadiran', function (Blueprint $table) {
                $table->dropColumn('waktu_pulang');
            });
        }

        if (Schema::hasColumn('rapat', 'presensi_pulang_kedaluwarsa')) {
            Schema::table('rapat', function (Blueprint $table) {
                $table->dropColumn('presensi_pulang_kedaluwarsa');
            });
        }

        if (Schema::hasColumn('rapat', 'kode_presensi_pulang')) {
            Schema::table('rapat', function (Blueprint $table) {
                $table->dropColumn('kode_presensi_pulang');
            });
        }
    }
};
