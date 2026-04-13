<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->string('company', 255)->nullable()->after('phone');
            $table->string('job_title', 255)->nullable()->after('company');
        });

        Schema::table('participant_accounts', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->string('company', 255)->nullable()->after('phone');
            $table->string('job_title', 255)->nullable()->after('company');
        });

        Schema::table('assessment_links', function (Blueprint $table) {
            $table->dropColumn('collect_department');
            $table->boolean('collect_company')->default(false)->after('collect_phone');
            $table->boolean('collect_job_title')->default(false)->after('collect_company');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['company', 'job_title']);
            $table->string('department', 255)->nullable();
        });

        Schema::table('participant_accounts', function (Blueprint $table) {
            $table->dropColumn(['company', 'job_title']);
            $table->string('department', 255)->nullable();
        });

        Schema::table('assessment_links', function (Blueprint $table) {
            $table->dropColumn(['collect_company', 'collect_job_title']);
            $table->boolean('collect_department')->default(false);
        });
    }
};
