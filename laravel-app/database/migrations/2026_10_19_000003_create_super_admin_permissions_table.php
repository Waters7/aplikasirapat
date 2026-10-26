<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('super_admin_permissions')) {
            Schema::create('super_admin_permissions', function (Blueprint $table) {
                $table->integer('pegawai_id')->primary();
                $table->boolean('buat_rapat')->default(false);
                $table->boolean('materi')->default(false);
                $table->boolean('rekaman')->default(false);
                $table->boolean('notulen')->default(false);
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->cascadeOnDelete();
            });

            return;
        }

        if (DB::getDriverName() === 'mysql') {
            $column = DB::table('information_schema.COLUMNS')
                ->select('COLUMN_TYPE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'super_admin_permissions')
                ->where('COLUMN_NAME', 'pegawai_id')
                ->value('COLUMN_TYPE');
            if ($column === 'int(10) unsigned') {
                DB::statement('ALTER TABLE super_admin_permissions MODIFY pegawai_id INT NOT NULL');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin_permissions');
    }
};
