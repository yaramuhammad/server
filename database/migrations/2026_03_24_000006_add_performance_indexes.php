<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->index('assessment_id');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->index('test_attempt_id');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropIndex(['assessment_id']);
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex(['test_attempt_id']);
        });
    }
};
