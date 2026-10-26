<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_access_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->boolean('buat_rapat')->default(false);
            $table->boolean('materi')->default(false);
            $table->boolean('rekaman')->default(false);
            $table->boolean('notulen')->default(false);
            $table->boolean('lihat_daftar_hadir')->default(false);
        });

        $managerIds = DB::table('admin_pegawai as ap')
            ->leftJoin('super_admin_permissions as sap', 'sap.pegawai_id', '=', 'ap.pegawai_id')
            ->where(function ($query) {
                $query->whereNull('sap.pegawai_id')->orWhere('sap.is_super_admin', false);
            })
            ->pluck('ap.pegawai_id');
        $settings = ['id' => 1];

        foreach (['buat_rapat', 'materi', 'rekaman', 'notulen', 'lihat_daftar_hadir'] as $permission) {
            $settings[$permission] = $managerIds->isNotEmpty()
                && DB::table('super_admin_permissions')
                    ->whereIn('pegawai_id', $managerIds)
                    ->where($permission, true)
                    ->count() === $managerIds->count();
        }

        DB::table('manager_access_settings')->insert($settings);
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_access_settings');
    }
};
