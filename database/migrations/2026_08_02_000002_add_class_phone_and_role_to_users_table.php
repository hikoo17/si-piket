<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('id')->constrained('classes')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['admin', 'guru', 'km', 'siswa'])->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['class_id', 'phone', 'role']);
        });
    }
};
