<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('super_admin_permissions', 'is_super_admin')) {
            Schema::table('super_admin_permissions', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('pegawai_id');
            });
        }

        // Migrate: pegawai yang ada di admin_pegawai tapi juga di super_admin_permissions
        // dan semua permission-nya = 1 (semua aktif) → kemungkinan besar adalah super admin lama
        // Kita set is_super_admin = 1 jika semua 6 permission = 1
        DB::statement("
            UPDATE super_admin_permissions
            SET is_super_admin = 1
            WHERE pegawai_id NOT IN (SELECT pegawai_id FROM admin_pegawai)
              AND buat_rapat = 1
              AND materi = 1
              AND rekaman = 1
              AND notulen = 1
              AND lihat_daftar_hadir = 1
              AND kelola_ruang = 1
        ");
    }

    public function down(): void
    {
        Schema::table('super_admin_permissions', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
