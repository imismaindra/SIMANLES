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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->string('kategori', 80);
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan');
            $table->date('tanggal');
            $table->string('referensi_id', 36)->nullable();
            $table->timestamps();

            $table->index(['cabang_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
