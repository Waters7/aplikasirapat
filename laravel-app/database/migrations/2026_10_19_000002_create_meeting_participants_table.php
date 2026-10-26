<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('meeting_participants')) {
            Schema::create('meeting_participants', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('rapat_id');
                $table->integer('pegawai_id');
                $table->unique(['rapat_id', 'pegawai_id']);
                $table->foreign('rapat_id')->references('id')->on('rapat')->cascadeOnDelete();
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->cascadeOnDelete();
            });

            return;
        }

        if (DB::getDriverName() === 'mysql') {
            $columns = DB::table('information_schema.COLUMNS')
                ->select('COLUMN_NAME', 'COLUMN_TYPE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'meeting_participants')
                ->whereIn('COLUMN_NAME', ['rapat_id', 'pegawai_id'])
                ->get()
                ->keyBy('COLUMN_NAME');

            foreach (['rapat_id', 'pegawai_id'] as $column) {
                if (($columns[$column]->COLUMN_TYPE ?? null) === 'int(10) unsigned') {
                    DB::statement("ALTER TABLE meeting_participants MODIFY {$column} INT NOT NULL");
                }
            }

            $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'meeting_participants')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->pluck('COLUMN_NAME')
                ->all();

            Schema::table('meeting_participants', function (Blueprint $table) use ($foreignKeys) {
                if (!in_array('rapat_id', $foreignKeys, true)) {
                    $table->foreign('rapat_id')->references('id')->on('rapat')->cascadeOnDelete();
                }
                if (!in_array('pegawai_id', $foreignKeys, true)) {
                    $table->foreign('pegawai_id')->references('id')->on('pegawai')->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
    }
};
