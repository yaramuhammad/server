<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Per-link control over what participants can see of their own results:
 *   allow_profile_view     — results page + portal psycho-profile (on screen, printing blocked)
 *   allow_profile_download — psycho-profile PDF download
 *
 * Replaces assessments.show_results_to_participant, which was only enforced on the
 * post-assessment results page. Existing links inherit that value for both options.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_links', function (Blueprint $table) {
            $table->boolean('allow_profile_view')->default(false)->after('collect_gender');
            $table->boolean('allow_profile_download')->default(false)->after('allow_profile_view');
        });

        DB::table('assessment_links')
            ->whereIn('assessment_id', DB::table('assessments')->where('show_results_to_participant', true)->select('id'))
            ->update(['allow_profile_view' => true, 'allow_profile_download' => true]);
    }

    public function down(): void
    {
        Schema::table('assessment_links', function (Blueprint $table) {
            $table->dropColumn(['allow_profile_view', 'allow_profile_download']);
        });
    }
};
