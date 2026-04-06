<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('title')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('password')->nullable();
            $table->boolean('collect_name')->default(true);
            $table->boolean('collect_email')->default(false);
            $table->boolean('collect_phone')->default(false);
            $table->boolean('collect_department')->default(false);
            $table->boolean('collect_age')->default(false);
            $table->boolean('collect_gender')->default(false);
            $table->jsonb('custom_fields')->nullable();
            $table->jsonb('welcome_message')->nullable();
            $table->jsonb('completion_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'starts_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_links');
    }
};
