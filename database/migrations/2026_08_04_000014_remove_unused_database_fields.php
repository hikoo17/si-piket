<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('piket_log_attempts');

        Schema::table('piket_logs', function (Blueprint $table) {
            $table->dropColumn([
                'wa_notif_sent',
                'location_captured_at',
                'ip_address',
                'user_agent',
            ]);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('google_place_id');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('google_place_id')->nullable()->after('address');
        });

        Schema::table('piket_logs', function (Blueprint $table) {
            $table->boolean('wa_notif_sent')->default(false)->after('distance_meters');
            $table->timestamp('location_captured_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
        });

        Schema::create('piket_log_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piket_log_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->integer('distance_meters')->nullable();
            $table->string('status');
            $table->string('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }
};
