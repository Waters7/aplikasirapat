<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_pegawai') || ! Schema::hasTable('super_admin_permissions')) {
            return;
        }

        DB::table('super_admin_permissions')
            ->where('is_super_admin', true)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('admin_pegawai')
                    ->whereColumn('admin_pegawai.pegawai_id', 'super_admin_permissions.pegawai_id');
            })
            ->where(function ($query) {
                foreach (['buat_rapat', 'materi', 'rekaman', 'notulen', 'lihat_daftar_hadir', 'kelola_ruang'] as $permission) {
                    $query->orWhere($permission, false);
                }
            })
            ->update(['is_super_admin' => false]);
    }

    public function down(): void
    {
        // Restoring the previous values could regrant the incorrect role.
    }
};
