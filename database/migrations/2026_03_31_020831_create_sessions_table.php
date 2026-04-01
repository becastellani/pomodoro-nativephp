<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pomodoro_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['focus', 'short_break', 'long_break']);
            $table->enum('status', ['completed', 'interrupted']);
            $table->integer('duration_minutes');
            $table->integer('elapsed_seconds')->default(0);
            $table->datetime('started_at');
            $table->datetime('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pomodoro_sessions');
    }
};