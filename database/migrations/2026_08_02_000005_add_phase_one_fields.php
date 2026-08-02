<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->time('upload_start_time')->default('05:00:00');
            $table->time('upload_deadline')->default('17:00:00');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->unique(['school_id', 'name']);
        });

        Schema::table('piket_logs', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->unique(['schedule_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('piket_logs', function (Blueprint $table) {
            $table->dropUnique(['schedule_id', 'date']);
            $table->dropColumn('verified_at');
        });
        Schema::table('classes', fn (Blueprint $table) => $table->dropUnique(['school_id', 'name']));
        Schema::table('schools', fn (Blueprint $table) => $table->dropColumn(['upload_start_time', 'upload_deadline']));
    }
};
