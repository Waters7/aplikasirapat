<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('super_admin_permissions', 'lihat_daftar_hadir')) {
            Schema::table('super_admin_permissions', function (Blueprint $table) {
                $table->boolean('lihat_daftar_hadir')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('super_admin_permissions', 'lihat_daftar_hadir')) {
            Schema::table('super_admin_permissions', function (Blueprint $table) {
                $table->dropColumn('lihat_daftar_hadir');
            });
        }
    }
};
