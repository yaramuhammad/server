<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->string('scoring_type', 30)->default('simple')->after('scale_config');
            $table->jsonb('scoring_config')->nullable()->after('scoring_type');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('category_key', 100)->nullable()->after('is_required');
            $table->decimal('weight', 5, 2)->default(1.00)->after('category_key');
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->jsonb('score_details')->nullable()->after('score_average');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['scoring_type', 'scoring_config']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['category_key', 'weight']);
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn('score_details');
        });
    }
};
