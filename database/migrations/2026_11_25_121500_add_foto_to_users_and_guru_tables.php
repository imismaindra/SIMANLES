<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('foto', 255)->nullable()->after('is_active');
            });
        }

        if (Schema::hasTable('guru') && ! Schema::hasColumn('guru', 'foto')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->string('foto', 255)->nullable()->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }

        if (Schema::hasTable('guru') && Schema::hasColumn('guru', 'foto')) {
            Schema::table('guru', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }
    }
};
