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
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->boolean('is_perusahaan_terdaftar')->default(true)->after('id_perusahaan');
            $table->string('nama_perusahaan_manual')->nullable()->after('is_perusahaan_terdaftar');
            $table->string('alamat_perusahaan_manual')->nullable()->after('nama_perusahaan_manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn(['is_perusahaan_terdaftar', 'nama_perusahaan_manual', 'alamat_perusahaan_manual']);
        });
    }
};
