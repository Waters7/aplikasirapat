<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pegawai') && !Schema::hasColumn('pegawai', 'password_change_required')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->boolean('password_change_required')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pegawai') && Schema::hasColumn('pegawai', 'password_change_required')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->dropColumn('password_change_required');
            });
        }
    }
};
