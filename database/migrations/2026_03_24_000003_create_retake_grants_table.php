<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retake_grants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('granted_at');
            $table->timestamp('used_at')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['participant_id', 'assessment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retake_grants');
    }
};
