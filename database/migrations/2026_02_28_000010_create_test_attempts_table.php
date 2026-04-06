<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_link_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('score_raw', 10, 2)->nullable();
            $table->decimal('score_max', 10, 2)->nullable();
            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->decimal('score_average', 5, 2)->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->timestamps();

            $table->unique(['participant_id', 'test_id', 'assessment_link_id']);
            $table->index('status');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
