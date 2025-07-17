<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perusahaan', function (Blueprint $table) {

            $table->tinyIncrements('id_perusahaan');
            $table->string('nama_perusahaan', 50);
            $table->text('alamat_perusahaan');
            $table->string('email_perusahaan', 50)->nullable();
            $table->string('logo_perusahaan')->nullable();
        });

        Schema::create('pembimbing_perusahaan', function (Blueprint $table) {
            $table->smallIncrements('id_pembimbing');
            $table->unsignedTinyInteger('id_perusahaan');
            $table->string('nama', 75);
            $table->string('no_hp', 17);

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
        });

        Schema::create('kontak_perusahaan', function (Blueprint $table) {
            $table->increments('id_kontak');
            $table->unsignedTinyInteger('id_perusahaan');
            $table->string('kontak_perusahaan', 17);

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_perusahaan')->nullable();
            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'id_perusahaan')) {
                $table->dropForeign(['id_perusahaan']);
                $table->dropColumn('id_perusahaan');
            }
        });
        Schema::dropIfExists('kontak_perusahaan');
        Schema::dropIfExists('pembimbing_perusahaan');
        Schema::dropIfExists('perusahaan');
    }
};
