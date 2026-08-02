<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piket_logs', function (Blueprint $table) {
            $table->decimal('accuracy_meters', 8, 2)->nullable()->after('longitude');
            $table->timestamp('location_captured_at')->nullable();
            $table->timestamp('photo_captured_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('piket_logs', fn (Blueprint $table) => $table->dropColumn(['accuracy_meters', 'location_captured_at', 'photo_captured_at', 'ip_address', 'user_agent']));
    }
};
