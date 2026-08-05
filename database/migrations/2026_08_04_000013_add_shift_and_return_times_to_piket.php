<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piket_schedules', function (Blueprint $table) {
            // MySQL may use the old unique index to support the user_id FK.
            // Add a replacement single-column index before removing it.
            $table->index('user_id', 'piket_schedules_user_id_fk_index');
            $table->dropUnique('piket_schedules_user_id_day_of_week_unique');
            $table->string('shift')->default('morning')->after('day_of_week');
            $table->unique(['user_id', 'day_of_week', 'shift']);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->time('return_upload_start_time')->default('14:00:00')->after('upload_deadline');
            $table->time('return_upload_deadline')->default('17:00:00')->after('return_upload_start_time');
        });
    }

    public function down(): void
    {
        Schema::table('piket_schedules', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'day_of_week', 'shift']);
            $table->dropColumn('shift');
            $table->unique(['user_id', 'day_of_week'], 'piket_schedules_user_id_day_of_week_unique');
            $table->dropIndex('piket_schedules_user_id_fk_index');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['return_upload_start_time', 'return_upload_deadline']);
        });
    }
};
