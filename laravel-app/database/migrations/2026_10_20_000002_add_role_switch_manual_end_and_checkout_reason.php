<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rapat', 'ended_at')) {
            Schema::table('rapat', function (Blueprint $table) {
                $table->dateTime('ended_at')->nullable();
            });
        }

        if (!Schema::hasColumn('kehadiran', 'alasan_pulang')) {
            Schema::table('kehadiran', function (Blueprint $table) {
                $table->text('alasan_pulang')->nullable();
            });
        }

        $driver = DB::getDriverName();
        $pegawaiIdType = $driver === 'mysql'
            ? DB::table('information_schema.COLUMNS')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'pegawai')
                ->where('COLUMN_NAME', 'id')
                ->value('COLUMN_TYPE')
            : null;
        $unsigned = $pegawaiIdType === 'int(10) unsigned';

        if (!Schema::hasTable('admin_pegawai')) {
            Schema::create('admin_pegawai', function (Blueprint $table) use ($unsigned) {
                $employeeId = $unsigned
                    ? $table->unsignedInteger('pegawai_id')
                    : $table->integer('pegawai_id');
                $employeeId->primary();
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->cascadeOnDelete();
            });
        } elseif ($driver === 'mysql') {
            $pivotIdType = DB::table('information_schema.COLUMNS')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'admin_pegawai')
                ->where('COLUMN_NAME', 'pegawai_id')
                ->value('COLUMN_TYPE');
            $expectedType = $unsigned ? 'int(10) unsigned' : 'int(11)';
            if ($pivotIdType !== $expectedType) {
                DB::statement('ALTER TABLE admin_pegawai MODIFY pegawai_id '.($unsigned ? 'INT UNSIGNED' : 'INT').' NOT NULL');
            }

            $foreignKeyExists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'admin_pegawai')
                ->where('COLUMN_NAME', 'pegawai_id')
                ->where('REFERENCED_TABLE_NAME', 'pegawai')
                ->exists();
            if (!$foreignKeyExists) {
                DB::statement('ALTER TABLE admin_pegawai ADD CONSTRAINT admin_pegawai_pegawai_id_foreign FOREIGN KEY (pegawai_id) REFERENCES pegawai (id) ON DELETE CASCADE');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_pegawai');

        if (Schema::hasColumn('kehadiran', 'alasan_pulang')) {
            Schema::table('kehadiran', function (Blueprint $table) {
                $table->dropColumn('alasan_pulang');
            });
        }

        if (Schema::hasColumn('rapat', 'ended_at')) {
            Schema::table('rapat', function (Blueprint $table) {
                $table->dropColumn('ended_at');
            });
        }
    }
};
