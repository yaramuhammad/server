<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->jsonb('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_reverse_scored')->default(false);
            $table->jsonb('scale_override')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index(['test_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
