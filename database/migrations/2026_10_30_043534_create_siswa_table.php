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
        Schema::create('siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('tempat_lahir', 100)->default('');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('nama_ibu', 150);
            $table->string('no_telp_ortu', 30);
            $table->enum('jenjang', ['TK', 'SD', 'SMP', 'SMA_SMK']);
            $table->unsignedTinyInteger('kelas');
            $table->foreignUuid('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->enum('status', ['aktif', 'cuti', 'keluar'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cabang_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
