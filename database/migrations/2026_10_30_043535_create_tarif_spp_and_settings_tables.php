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
        Schema::create('tarif_spp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('jenjang', ['TK', 'SD', 'SMP', 'SMA_SMK']);
            $table->unsignedTinyInteger('kelas_min');
            $table->unsignedTinyInteger('kelas_max');
            $table->decimal('nominal', 12, 2);
            $table->date('berlaku_sejak');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('kunci', 60)->primary();
            $table->string('nilai', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('tarif_spp');
    }
};
