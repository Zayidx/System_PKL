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
        Schema::table('prakerin', function (Blueprint $table) {
            $table->enum('status_prakerin', ['aktif', 'selesai', 'dibatalkan'])->default('aktif')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prakerin', function (Blueprint $table) {
            $table->dropColumn('status_prakerin');
        });
    }
};
