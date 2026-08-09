<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'guru_piket')->update(['role' => 'wali_kelas']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'wali_kelas', 'km', 'siswa') DEFAULT 'siswa' NOT NULL");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('siswa')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru_piket', 'wali_kelas', 'km', 'siswa') DEFAULT 'siswa' NOT NULL");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('siswa')->change();
            });
        }
    }
};
