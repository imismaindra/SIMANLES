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
        Schema::create('guru', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama', 150);
            $table->string('email', 190);
            $table->string('no_telp', 30);
            $table->string('no_pegawai', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jenjang', ['TK', 'SD', 'SMP', 'SMA_SMK'])->default('SD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('guru_cabang', function (Blueprint $table) {
            $table->foreignUuid('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignUuid('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->primary(['guru_id', 'cabang_id']);
        });

        Schema::create('guru_jenjang', function (Blueprint $table) {
            $table->foreignUuid('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->enum('jenjang', ['TK', 'SD', 'SMP', 'SMA_SMK']);
            $table->primary(['guru_id', 'jenjang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_jenjang');
        Schema::dropIfExists('guru_cabang');
        Schema::dropIfExists('guru');
    }
};
