<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('piket_log_attempts');
    }
};
