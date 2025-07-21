<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perusahaan', function (Blueprint $table) {
            // PERBAIKAN: Menggunakan increments() agar konsisten.
            $table->increments('id_perusahaan');
            $table->string('nama_perusahaan', 100);
            $table->text('alamat_perusahaan');
            $table->string('email_perusahaan', 100)->nullable();
            // PENAMBAHAN: Kolom kontak yang hilang
            $table->string('kontak_perusahaan', 20)->nullable();
            $table->string('logo_perusahaan')->nullable();
        });

        Schema::create('pembimbing_perusahaan', function (Blueprint $table) {
            // PERBAIKAN: Menggunakan increments() agar konsisten.
            $table->increments('id_pembimbing');
            // PERBAIKAN: Tipe data disesuaikan dengan primary key di 'perusahaan'.
            $table->unsignedInteger('id_perusahaan');
            $table->string('nama', 75);
            $table->string('no_hp', 17);

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
        });

        Schema::create('kontak_perusahaan', function (Blueprint $table) {
            $table->increments('id_kontak');
            // PERBAIKAN: Tipe data disesuaikan dengan primary key di 'perusahaan'.
            $table->unsignedInteger('id_perusahaan');
            $table->string('kontak_perusahaan', 17);

            $table->foreign('id_perusahaan')->references('id_perusahaan')->on('perusahaan')->onDelete('cascade');
        });

        Schema::table('siswa', function (Blueprint $table) {
            // PERBAIKAN: Tipe data disesuaikan dengan primary key di 'perusahaan'.
            $table->unsignedInteger('id_perusahaan')->nullable();
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
