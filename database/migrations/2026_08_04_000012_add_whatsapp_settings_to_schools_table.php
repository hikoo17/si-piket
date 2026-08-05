<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('whatsapp_enabled')->default(false);
            $table->time('whatsapp_send_time')->default('06:00:00');
            $table->text('whatsapp_message_template')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_enabled', 'whatsapp_send_time', 'whatsapp_message_template']);
        });
    }
};
