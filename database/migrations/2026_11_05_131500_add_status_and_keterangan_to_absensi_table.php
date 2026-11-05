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
            if (! Schema::hasColumn('absensi', 'status')) {
                $table->string('status', 20)->default('hadir')->after('jam_pulang');
            }
            if (! Schema::hasColumn('absensi', 'keterangan')) {
                $table->string('keterangan', 255)->nullable()->after('status');
            }
            if (! Schema::hasColumn('absensi', 'metode')) {
                $table->string('metode', 20)->default('qr')->after('keterangan');
            }
        });

        Schema::table('qr_token', function (Blueprint $table) {
            // Ubah tipe dari enum ke string agar fleksibel mendukung 'auto', 'datang', 'pulang'
            $table->string('tipe', 20)->default('auto')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (Schema::hasColumn('absensi', 'metode')) {
                $table->dropColumn('metode');
            }
            if (Schema::hasColumn('absensi', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            if (Schema::hasColumn('absensi', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('qr_token', function (Blueprint $table) {
            $table->enum('tipe', ['datang', 'pulang'])->default('datang')->change();
        });
    }
};
