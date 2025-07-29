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
        Schema::table('siswa', function (Blueprint $table) {
            $table->unsignedInteger('nip_pembimbing_sekolah')->nullable()->after('kontak_siswa');
            $table->foreign('nip_pembimbing_sekolah')->references('nip_pembimbing_sekolah')->on('pembimbing_sekolah')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['nip_pembimbing_sekolah']);
            $table->dropColumn('nip_pembimbing_sekolah');
        });
    }
};
