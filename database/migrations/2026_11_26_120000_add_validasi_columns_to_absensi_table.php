<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi', 'status_validasi')) {
                $table->string('status_validasi', 20)->nullable()->after('status');
            }
            if (! Schema::hasColumn('absensi', 'is_manual_override')) {
                $table->boolean('is_manual_override')->default(false)->after('status_validasi');
            }
            if (! Schema::hasColumn('absensi', 'alasan_validasi')) {
                $table->string('alasan_validasi', 255)->nullable()->after('is_manual_override');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (Schema::hasColumn('absensi', 'alasan_validasi')) {
                $table->dropColumn('alasan_validasi');
            }
            if (Schema::hasColumn('absensi', 'is_manual_override')) {
                $table->dropColumn('is_manual_override');
            }
            if (Schema::hasColumn('absensi', 'status_validasi')) {
                $table->dropColumn('status_validasi');
            }
        });
    }
};
