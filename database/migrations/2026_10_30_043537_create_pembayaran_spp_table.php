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
        Schema::create('pembayaran_spp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignUuid('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('nominal', 12, 2);
            $table->enum('status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->string('nama_pembayar', 150)->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('no_kwitansi', 60)->nullable();
            $table->string('kwitansi_url', 255)->nullable();
            $table->dateTime('dikirim_at')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'bulan', 'tahun'], 'unique_spp_siswa_bulan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_spp');
    }
};
