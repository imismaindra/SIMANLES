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
        Schema::create('absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('guru_id')->nullable()->constrained('guru')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->date('tanggal');
            $table->dateTime('jam_datang')->nullable();
            $table->dateTime('jam_pulang')->nullable();
            $table->timestamps();

            $table->unique(['guru_id', 'tanggal'], 'unique_absensi_guru_tgl');
        });

        Schema::create('qr_token', function (Blueprint $table) {
            $table->string('token', 20)->primary();
            $table->foreignUuid('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->enum('tipe', ['datang', 'pulang'])->default('datang');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_token');
        Schema::dropIfExists('absensi');
    }
};
