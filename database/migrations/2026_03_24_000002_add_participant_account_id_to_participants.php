<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->foreignId('participant_account_id')
                ->nullable()
                ->after('assessment_link_id')
                ->constrained('participant_accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['participant_account_id']);
            $table->dropColumn('participant_account_id');
        });
    }
};
