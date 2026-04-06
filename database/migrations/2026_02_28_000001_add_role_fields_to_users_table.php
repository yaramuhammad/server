<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->string('role', 20)->default('admin')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('preferred_locale', 5)->default('en')->after('is_active');
            $table->softDeletes();

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropSoftDeletes();
            $table->dropColumn(['uuid', 'role', 'is_active', 'preferred_locale']);
        });
    }
};
