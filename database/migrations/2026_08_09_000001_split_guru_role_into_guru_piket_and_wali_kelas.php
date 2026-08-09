<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role_new', ['admin', 'guru_piket', 'wali_kelas', 'km', 'siswa'])->after('phone');
        });

        DB::table('users')->where('role', 'guru')->update([
            'role_new' => DB::raw("CASE WHEN class_id IS NULL THEN 'guru_piket' ELSE 'wali_kelas' END"),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->renameColumn('role_new', 'role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role_old', ['admin', 'guru', 'km', 'siswa'])->after('phone');
        });

        DB::table('users')->whereIn('role', ['guru_piket', 'wali_kelas'])->update([
            'role_old' => 'guru',
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->renameColumn('role_old', 'role');
        });
    }
};
