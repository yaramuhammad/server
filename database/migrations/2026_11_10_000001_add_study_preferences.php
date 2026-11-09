<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ranked study preferences: a link can ask each participant for their four study
 * preferences, in order, before the tests. They are stored on the per-link participant
 * record and shown in the psycho-profile next to the scores.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_links', function (Blueprint $table) {
            $table->boolean('collect_study_preferences')->default(false)->after('collect_gender');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->json('study_preferences')->nullable()->after('custom_data');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_links', function (Blueprint $table) {
            $table->dropColumn('collect_study_preferences');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('study_preferences');
        });
    }
};
