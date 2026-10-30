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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['pusat', 'cabang', 'guru'])->default('cabang')->after('email');
            $table->foreignUuid('cabang_id')->nullable()->after('role')->constrained('cabang')->nullOnDelete();
            $table->string('no_pegawai', 50)->nullable()->after('cabang_id');
            $table->date('tanggal_lahir')->nullable()->after('no_pegawai');
            $table->boolean('is_active')->default(true)->after('tanggal_lahir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn(['role', 'cabang_id', 'no_pegawai', 'tanggal_lahir', 'is_active']);
        });
    }
};
