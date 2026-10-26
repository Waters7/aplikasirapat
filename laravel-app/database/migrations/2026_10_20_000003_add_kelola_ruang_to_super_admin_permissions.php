<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('super_admin_permissions') && !Schema::hasColumn('super_admin_permissions', 'kelola_ruang')) {
            Schema::table('super_admin_permissions', function (Blueprint $table) {
                $table->boolean('kelola_ruang')->default(false)->after('lihat_daftar_hadir');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('super_admin_permissions') && Schema::hasColumn('super_admin_permissions', 'kelola_ruang')) {
            Schema::table('super_admin_permissions', function (Blueprint $table) {
                $table->dropColumn('kelola_ruang');
            });
        }
    }
};
